<?php
session_start();
include 'connect.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

// Connect to the database
$conn = OpenCon();
if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id']; // Ensure this is stored in the session after login

// Check if all required POST data is set
if (!isset($_POST['event_date'], $_POST['event_title'], $_POST['event_time'], $_POST['event_venue'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing event data']);
    exit();
}

// Get event details from the POST data
$event_date = $_POST['event_date'];
$event_title = $_POST['event_title'];
$event_time = $_POST['event_time'];
$event_venue = $_POST['event_venue'];

// Debugging: Log the received data
error_log("Received data: $event_date, $event_title, $event_time, $event_venue");

// Validate event date
try {
    $current_date = new DateTime();
    $selected_date = new DateTime($event_date);

    if ($selected_date < $current_date) {
        echo json_encode(['status' => 'error', 'message' => 'Event date cannot be in the past']);
        exit();
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid date format: ' . $e->getMessage()]);
    exit();
}

// Insert the new event into the database
$sql = "INSERT INTO events (user_id, event_date, event_title, event_time, event_venue) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit();
}

$stmt->bind_param("issss", $user_id, $event_date, $event_title, $event_time, $event_venue);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Event added successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error adding event: ' . $stmt->error]);
}

// Close the database connection
CloseCon($conn);
?>
