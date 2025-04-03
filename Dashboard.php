<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admission_number'])) {
    // Redirect to the home page or login page if not logged in
    header("Location: Login.html");
    exit();
}

// Include database connection
include 'connect.php';

// Open database connection
$conn = OpenCon();
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch events from the database
$sql = "SELECT id, title, description, event_date, event_time, location FROM Admin_events ORDER BY event_date ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
    margin-top: 160px;
    padding: 2rem;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    padding: 1rem;
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
    display: flex;
    flex-direction: column;
}

.event-title {
    font-size: 1.5rem;
    font-weight: bold;
}

.event-details {
    margin-bottom: 1rem;
}

.event-description {
    margin-bottom: 1rem;
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

@media (max-width: 768px) {
    .top-nav ul {
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: center;
    }

    .main-content {
        margin-top: 140px;
        padding: 1rem;
    }

    .logo h1 {
        font-size: 1.8rem;
    }
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

            <li><a href="Home_page.html">Home</a></li>
            <li><a href="Dashboard.php">Dashboard</a></li>
            <li><a href="calendar.php">Calendar</a></li>
            <li><a href="Account.php">Account</a></li>

        </ul>
    </nav>

    <main class="main-content">
        <div class="events-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($event = $result->fetch_assoc()): ?>
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
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No upcoming events.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <!-- Your existing footer content -->
    </footer>
</body>
</html>
