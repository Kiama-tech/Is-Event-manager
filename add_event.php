<?php
session_start();
include 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: Login.html");
    exit();
}

// Connect to the database
$conn = OpenCon();
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get the user ID from the session
$user_id = $_SESSION['user_id']; // Make sure this is stored in session after login

// Get event details from the form
$event_date = $_POST['event_date'];
$event_title = $_POST['event_title'];
$event_time = $_POST['event_time'];
$event_venue = $_POST['event_venue'];

// Insert the new event into the database
$sql = "INSERT INTO events (user_id, event_date, event_title, event_time, event_venue) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issss", $user_id, $event_date, $event_title, $event_time, $event_venue);

if ($stmt->execute()) {
    header("Location: Account.php"); // Redirect back to the account page after success
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Close the database connection
CloseCon($conn);
?>
