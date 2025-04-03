<?php
// admin_login.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $admin_key = trim($_POST['admin_key']);

    // Connect to the database
    $conn = OpenCon();
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Check if the user exists and is an admin
    $sql = "SELECT * FROM user WHERE email = ? AND user_type = 'Admin'";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password and admin key
        if (password_verify($password, $user['password_hash']) && $admin_key === $user['admin_key']) {
            // Start a session and store user data
            session_start();
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['admin_key'] = $user['admin_key']; // Ensure admin_key is set in the session

            // Redirect to admin profile page
            header("Location: Admin_profile.php");
            exit();
        } else {
            echo "Invalid password or admin key! <a href='Admin-login.html'>Go back</a>";
        }
    } else {
        echo "Admin user not found! <a href='Admin-login.html'>Go back</a>";
    }

    $stmt->close();
    CloseCon($conn);
}
?>
