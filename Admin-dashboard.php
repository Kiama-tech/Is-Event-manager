<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection securely
if (!file_exists('connect.php')) {
    die("Error: Database connection file missing.");
}
include 'connect.php';

// Open database connection
$conn = OpenCon();
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin' || empty($_SESSION['admin_key'])) {
    header("Location: Admin-login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Events</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f8f9fa;
            min-height: 100vh;
        }

        header {
            background: linear-gradient(135deg, var(--primary), #1a252f);
            color: white;
            padding: 1rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo h1 {
            background: linear-gradient(45deg, var(--secondary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-size: 2.2rem;
            letter-spacing: 1px;
        }

        .top-nav {
            background: rgba(255,255,255,0.95);
            padding: 1rem;
            position: fixed;
            top: 80px;
            width: 100%;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.1);
            z-index: 999;
        }

        .top-nav ul {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            list-style: none;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0;
        }

        .top-nav a {
            color: var(--primary);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
            position: relative;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .top-nav a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--secondary);
            transition: width 0.3s;
        }

        .top-nav a:hover::after {
            width: 100%;
        }

        .top-nav .fa {
            font-size: 1.2rem;
        }

        .main-content {
            display: flex;
            margin-top: 160px;
            padding: 2rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .event-form {
            width: 30%;
        }

        .events-list {
            width: 70%;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .event-card {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            position: relative;
            padding: 1.5rem;
        }

        .event-card:hover {
            transform: translateY(-5px);
        }

        .event-info {
            position: relative;
        }

        .event-title {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .event-details {
            color: #555;
            margin-bottom: 8px;
        }

        .event-description {
            color: #777;
        }

        .action-buttons {
            margin-top: 10px;
        }

        .action-buttons button {
            margin-right: 5px;
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .action-buttons button:hover {
            background: var(--secondary);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--primary);
            margin-bottom: -8px;
        }

        .form-input {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.3);
        }

        .rsvp-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .rsvp-btn:hover {
            background: var(--secondary);
        }

        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
            }

            .event-form, .events-list {
                width: 100%;
            }
        }

        .footer {
            background: var(--primary);
            color: var(--light);
            padding: 40px 20px 20px;
            margin-top: 50px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }

        .footer-section h4 {
            color: var(--secondary);
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .footer-section p, .footer-section a {
            color: var(--light);
            line-height: 1.6;
            font-size: 0.9rem;
        }

        .footer-section a {
            text-decoration: none;
            display: block;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: var(--secondary);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .notification {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            position: relative;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .notification.success {
            background-color: rgba(46, 204, 113, 0.15);
            border-left: 4px solid #2ecc71;
            color: #27ae60;
        }
        
        .notification.error {
            background-color: rgba(231, 76, 60, 0.15);
            border-left: 4px solid #e74c3c;
            color: #c0392b;
        }
        
        .notification .close-btn {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s;
        }
        
        .notification .close-btn:hover {
            opacity: 1;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <h1>School Event Manager</h1>
        </div>
    </header>

    <nav class="top-nav">
        <ul>
            <li><a href="Admin-dashboard.php">Dashboard</a></li>
            <li><a href="Admin_profile.php">Account</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <div class="event-form">
            <form action="Admin_create.php" method="POST">
                <h2>CREATE EVENT</h2>
                <section class="registration-section animate-fade-in">
                    <div class="form-group">
                        <label class="form-label" for="event-name">Event Name</label>
                        <input type="text" class="form-input" id="event-name" name="title" required>

                        <label class="form-label" for="event-place">Event Location</label>
                        <input type="text" class="form-input" id="event-place" name="location" required>

                        <label class="form-label" for="event-date">Event Date</label>
                        <input type="date" class="form-input" id="event-date" name="event_date" required>

                        <label class="form-label" for="event-time">Event Time</label>
                        <input type="time" class="form-input" id="event-time" name="event_time" required>

                        <label class="form-label" for="event-description">Event Description</label>
                        <textarea class="form-input" id="event-description" name="description" rows="4" required></textarea>

                        <button type="submit" class="rsvp-btn" style="margin-top: 20px; width: 100%;">Create Event</button>
                    </div>
                </section>
            </form>
        </div>

        <div class="events-list">
            <?php
            // Fetch events from the database
            $sql = "SELECT id, title, description, event_date, event_time, location FROM Admin_events ORDER BY event_date ASC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0):
                while ($event = $result->fetch_assoc()):
            ?>
                    <div class="event-card">
                        <div class="event-info">
                            <div class="event-title"><?php echo htmlspecialchars($event['title']); ?></div>
                            <div class="event-details">
                                <strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?><br>
                                <strong>Time:</strong> <?php echo htmlspecialchars($event['event_time']); ?><br>
                                <strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?>
                            </div>
                            <div class="event-description">
                                <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                            </div>
                            <div class="action-buttons">
                                <button onclick="window.location.href='Admin_update.php?id=<?php echo $event['id']; ?>'">Update</button>
                                <button onclick="if(confirm('Are you sure you want to delete this event?')) { window.location.href='http://localhost/Event_manager/Admin_delete.php?id=<?php echo $event['id']; ?>'; }">Delete</button>
                            </div>
                        </div>
                    </div>
            <?php
                endwhile;
            else:
                echo "<p>No upcoming events.</p>";
            endif;
            ?>
        </div>
        <main class="main-content">
        <!-- Notification area -->
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="notification success">
                <span><?php echo $_SESSION['success_message']; ?></span>
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="notification error">
                <span><?php echo $_SESSION['error_message']; ?></span>
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <div class="event-form">
            <!-- Rest of your existing form code -->
        </div>

        <div class="events-list">
            <!-- Rest of your existing events list code -->
        </div>
    </main>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Contact Us</h4>
                <p>Email: eschoolventsystem@gmail.com</p>
                <p>Phone: (+254) 7-1234-5678</p>
                <p>Address: 123 Campus Road, Nairobi City</p>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="Login.html">Log-In</a></li>
                    <li><a href="signup.html">Sign-Up</a></li>
                    <li><a href="Dashboard.html">Dashboard</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© <span id="year"></span> School Event Management System. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
        document.getElementById('year').textContent = new Date().getFullYear();
        
        // Auto-hide notifications after 5 seconds
        setTimeout(function() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(function(notification) {
                if (notification) {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateY(-10px)';
                    notification.style.transition = 'opacity 0.5s, transform 0.5s';
                    
                    setTimeout(function() {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 500);
                }
            });
        }, 5000);
    </script>
</body>
</html>

<?php
// Close the database connection
CloseCon($conn);
?>
