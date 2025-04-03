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

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process form submission
    $event_id = mysqli_real_escape_string($conn, $_POST['event_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $event_date = mysqli_real_escape_string($conn, $_POST['event_date']);
    $event_time = mysqli_real_escape_string($conn, $_POST['event_time']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Update the event in database
    $sql = "UPDATE Admin_events SET 
            title = ?,
            location = ?,
            event_date = ?,
            event_time = ?,
            description = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $title, $location, $event_date, $event_time, $description, $event_id);

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Event updated successfully!";
        header("Location: Admin-dashboard.php");
    } else {
        $_SESSION['error_message'] = "Error updating event: " . $stmt->error;
        header("Location: Admin_update.php?id=" . $event_id);
    }

    $stmt->close();
    CloseCon($conn);
    exit();
}

// Display update form if not a POST request
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Error: No event ID provided.";
    exit();
}

$event_id = mysqli_real_escape_string($conn, $_GET['id']);

// Get event details
$sql = "SELECT id, title, description, event_date, event_time, location FROM Admin_events WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Error: Event not found.";
    $stmt->close();
    CloseCon($conn);
    exit();
}

$event = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Event - Admin Dashboard</title>
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

        .main-content {
            margin-top: 160px;
            padding: 2rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .update-form {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .form-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--primary);
            margin-bottom: -8px;
        }

        .form-input {
            padding: 12px 15px;
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

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .update-btn {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            flex: 1;
        }

        .update-btn:hover {
            background: #2980b9;
        }

        .cancel-btn {
            background: #95a5a6;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            flex: 1;
        }

        .cancel-btn:hover {
            background: #7f8c8d;
        }

        .footer {
            background: var(--primary);
            color: var(--light);
            padding: 40px 20px 20px;
            margin-top: 50px;
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
        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="notification error">
                <span><?php echo $_SESSION['error_message']; ?></span>
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <div class="update-form">
            <div class="form-header">
                <h2>Update Event</h2>
                <p>Make changes to the event details below</p>
            </div>
            
            <form action="Admin_update.php" method="POST">
                <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['id']); ?>">
                
                <div class="form-group">
                    <label class="form-label" for="event-name">Event Name</label>
                    <input type="text" class="form-input" id="event-name" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="event-place">Event Location</label>
                    <input type="text" class="form-input" id="event-place" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="event-date">Event Date</label>
                    <input type="date" class="form-input" id="event-date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="event-time">Event Time</label>
                    <input type="time" class="form-input" id="event-time" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="event-description">Event Description</label>
                    <textarea class="form-input" id="event-description" name="description" rows="4" required><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" class="update-btn">Update Event</button>
                    <button type="button" class="cancel-btn" onclick="window.location.href='Admin-dashboard.php'">Cancel</button>
                </div>
            </form>
        </div>
    </main>

    <footer class="footer">
        <!-- Footer content here -->
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>

<?php
// Close the database connection
CloseCon($conn);
?>