<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection securely
if (!file_exists('connect.php')) {
    die("Error: Database connection file missing.");
}
include 'connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin' || empty($_SESSION['admin_key'])) {
    header("Location: Admin-login.php");
    exit();
}

// Open database connection
$conn = OpenCon();
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch events from the database
$sql = "SELECT id, title, description, event_date, event_time, location FROM Admin_events ORDER BY event_date ASC";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Close the database connection
CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Events</title>
    <!-- Include your CSS and other head elements here -->
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
                            <div class="action-buttons">
                                <button onclick="window.location.href='update_event_form.php?id=<?php echo $event['id']; ?>'">Update</button>
                                <button onclick="if(confirm('Are you sure you want to delete this event?')) window.location.href='delete_event.php?id=<?php echo $event['id']; ?>'">Delete</button>
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
        <!-- Include your footer content here -->
    </footer>
</body>
</html>
