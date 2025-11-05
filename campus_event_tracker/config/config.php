<?php
$host = "localhost";
$user = "root";     // change if needed
$pass = "";         // change if needed
$db   = "campus_event_tracker";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

session_start();
?>
