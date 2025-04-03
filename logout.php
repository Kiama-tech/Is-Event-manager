<?php
session_start();

// Check the user type before destroying the session
$redirect_page = 'Login.html'; // Default to student login page
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'Admin') {
    $redirect_page = 'Admin_login.html';
}

// Destroy the session
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session

// Prevent caching of restricted pages
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to the appropriate login page
header("Location: $redirect_page");
exit();
?>
