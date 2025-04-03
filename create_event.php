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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = trim($_POST['location']);

    $sql = "INSERT INTO Admin_events (title, description, event_date, event_time, location) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssss", $title, $description, $event_date, $event_time, $location);
        $stmt->execute();
        $stmt->close();
        echo "Event added successfully!";
    } else {
        die("Error: Failed to prepare SQL statement.");
    }
}

// Close the database connection
CloseCon($conn);
?>
