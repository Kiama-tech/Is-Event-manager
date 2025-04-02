<?php
// Database connection
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "event_manager"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get events from the database
$sql = "SELECT * FROM events ORDER BY event_date";
$result = $conn->query($sql);

$events = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;  // Add each event to the array
    }
    echo json_encode($events);
} else {
    echo json_encode(["status" => "error", "message" => "No events found."]);
}

$conn->close();
?>
