<?php
include 'connect.php';

$conn = OpenCon(); // Initialize the connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $admission = trim($_POST['admission']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($admission) || empty($password) || empty($confirm_password)) {
        echo "All fields are required.";
    } elseif ($password !== $confirm_password) {
        echo "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, admission, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $admission, $hashed_password);

        if ($stmt->execute()) {
            echo "User created successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

CloseCon($conn); // Close the connection
?>