<?php
include 'connect.php'; // Make sure this path is correct

$conn = OpenCon();

if ($conn) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed!";
}

CloseCon($conn);
?>
