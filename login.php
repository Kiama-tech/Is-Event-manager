<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection
include 'connect.php';

// Redirect if the user is already logged in
if (isset($_SESSION['admission_number'])) {
    header("Location: Account.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and trim input
    $admission = trim($_POST['admission_number']);
    $password = trim($_POST['password']);

    // Debugging output
    echo "Adimission Number: $admission <br>";
    echo "Password: $password <br>";

    if (!$admission || !$password) {
        $error_message = "Error: All fields are required!";
        header("Location: Login.html?error=" . urlencode($error_message));
        exit();
    }

    // Open database connection
    $conn = OpenCon();
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Prepare SQL statement to fetch user details based on admission number
    $sql = "SELECT username, email, user_type, password_hash FROM user WHERE admission_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admission);
    $stmt->execute();
    $stmt->store_result();

    // Debugging: Check if user exists
    echo "Number of rows: " . $stmt->num_rows . "<br>";

    if ($stmt->num_rows > 0) {
        echo "User found!<br>"; // Debugging to see if user is found
        
        $stmt->bind_result($username, $email, $user_type, $hashed_password);
        if ($stmt->fetch() && password_verify($password, $hashed_password)) {
            // Regenerate session ID for security
            session_regenerate_id(true);

            // Store user data in session
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['admission_number'] = $admission;
            $_SESSION['user_type'] = $user_type;

            // Redirect to profile page
            header("Location: Account.php");
            exit();
        } else {
            // Incorrect password
            $error_message = "Error: Incorrect password!";
            header("Location: Login.html?error=" . urlencode($error_message));
            exit();
        }
    } else {
        // User not found
        $error_message = "Error: User not found!";
        header("Location: Login.html?error=" . urlencode($error_message));
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    CloseCon($conn);
}
?>
