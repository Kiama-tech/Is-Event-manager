<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'connect.php'; // Ensure this file correctly connects to MySQL

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $admission_number = isset($_POST['admission_number']) ? trim($_POST['admission_number']) : '';
    $user_type = $_POST['user_type'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $admin_key = isset($_POST['admin_key']) ? trim($_POST['admin_key']) : null;

    // Validate required fields
    if (empty($username) || empty($email) || empty($password) || empty($user_type)) {
        die("Error: Missing form fields! <a href='Signup.html'>Go back</a>");
    }

    if ($password !== $confirm_password) {
        die("Error: Passwords do not match! <a href='Signup.html'>Go back</a>");
    }

    // Connect to the database
    $conn = OpenCon();
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Check if email or admission number already exists
    $check_sql = "SELECT * FROM user WHERE email = ? OR admission_number = ?";
    $stmt_check = $conn->prepare($check_sql);
    if (!$stmt_check) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt_check->bind_param("ss", $email, $admission_number);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        die("Error: Email or Admission Number already exists! <a href='Signup.html'>Go back</a>");
    }
    $stmt_check->close();

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data based on user type
    if ($user_type === "Admin") {
        if (empty($admin_key) || $admin_key !== "2030cuba") { // Replace with actual admin key
            die("Error: Invalid Admin Key! <a href='Signup.html'>Go back</a>");
        }
        // Insert for Admin user
        $sql = "INSERT INTO user (username, email, user_type, admin_key, password_hash) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("sssss", $username, $email, $user_type, $admin_key, $hashed_password);

        // Execute statement
        if ($stmt->execute()) {
            header("Location: Admin_login.html"); // Redirect to admin login page
            exit();
        } else {
            echo "Error inserting data: " . $stmt->error;
        }
    } else {
        // Insert for non-Admin user
        $sql = "INSERT INTO user (username, email, admission_number, user_type, password_hash) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("sssss", $username, $email, $admission_number, $user_type, $hashed_password);

        // Execute statement
        if ($stmt->execute()) {
            header("Location: Login.html"); // Redirect to general login page
            exit();
        } else {
            echo "Error inserting data: " . $stmt->error;
        }
    }

    $stmt->close();
    CloseCon($conn);
}
?>
