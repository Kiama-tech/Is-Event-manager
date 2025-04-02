<?php
function OpenCon() {
    $servername = "localhost"; // or your database server
    $username = "root"; // your DB username
    $password = ""; // your DB password
    $dbname = "event_manager"; // your DB name

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function CloseCon($conn) {
    $conn->close();
}
?>
