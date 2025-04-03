<?php
function OpenCon() {
    $servername = "localhost"; // Change to your database server if needed
    $username = "root"; // Your database username
    $password = ""; // Your database password
    $dbname = "event_manager"; // Your database name

    // Enable error reporting for mysqli
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        $conn->set_charset("utf8mb4"); // Ensure correct character encoding
    } catch (mysqli_sql_exception $e) {
        die("Database connection failed: " . $e->getMessage());
    }

    return $conn;
}

function CloseCon($conn) {
    if ($conn instanceof mysqli) {
        $conn->close();
    }
}
?>
