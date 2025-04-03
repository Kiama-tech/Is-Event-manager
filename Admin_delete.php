<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection securely
if (!file_exists('connect.php')) {
    die("Error: Database connection file missing.");
}
include 'connect.php';

// Check if user is logged in as an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: Admin-login.php");
    exit();
}

// Open database connection
$conn = OpenCon();
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Validate event ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid event ID.");
}

$id = intval($_GET['id']); // Ensure ID is an integer

// Prepare and execute delete statement
$stmt = $conn->prepare("DELETE FROM Admin_events WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Redirect only if deletion is successful
    header("Location: Admin_dashboard.php");
    exit();
} else {
    // Show error before redirect
    die("Error deleting event: " . $stmt->error);
}

// Close statement and connection
$stmt->close();
CloseCon($conn);
?>
