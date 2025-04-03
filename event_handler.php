<?php
session_start(); // Start the session
header('Content-Type: application/json'); // Set content type to JSON

// Database connection
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "event_manager"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Determine the action based on the request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Fetch events from the database
    $sql = "SELECT * FROM events ORDER BY event_date";
    $result = $conn->query($sql);

    $events = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;  // Add each event to the array
        }
        echo json_encode($events);
    } else {
        echo json_encode([]);  // Return empty array if no events
    }
} elseif ($method === 'POST') {
    // Check if user is logged in (for adding new events)
    if (!isset($_SESSION['username'])) {
        echo json_encode(["status" => "error", "message" => "User not logged in"]);
        exit();
    }

    // Check if all required POST data is set
    if (!isset($_POST['event_date'], $_POST['event_title'], $_POST['event_time'], $_POST['event_venue'])) {
        echo json_encode(["status" => "error", "message" => "Missing event data"]);
        exit();
    }

    // Get event details from POST data
    $event_date = $_POST['event_date'];
    $event_title = $_POST['event_title'];
    $event_time = $_POST['event_time'];
    $event_venue = $_POST['event_venue'];

    // Debugging: Log the received data
    error_log("Received data: $event_date, $event_title, $event_time, $event_venue");

    // Check if we're updating or adding a new event
    if (isset($_POST['event_id']) && !empty($_POST['event_id'])) {
        // Update existing event
        $event_id = $_POST['event_id'];

        // Update the event in the database
        $sql = "UPDATE events SET event_date=?, event_title=?, event_time=?, event_venue=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $event_date, $event_title, $event_time, $event_venue, $event_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Event updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error updating event: " . $stmt->error]);
        }
    } else {
        // Add new event
        $user_id = $_SESSION['user_id']; // Ensure user_id is stored in session after login

        // Insert the new event into the database
        $sql = "INSERT INTO events (user_id, event_date, event_title, event_time, event_venue) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $user_id, $event_date, $event_title, $event_time, $event_venue);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Event added successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error adding event: " . $stmt->error]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

$conn->close();
?>
