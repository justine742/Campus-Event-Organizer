<?php
require_once "../config/config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only admin can delete
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['username'])) {
    $username = $conn->real_escape_string($_GET['username']);

    // Delete the student
    $deleteQuery = "DELETE FROM users WHERE username='$username' AND role='student'";
    if ($conn->query($deleteQuery)) {
        header("Location: studentlist.php?success=Student+removed+successfully");
        exit;
    } else {
        echo "Error removing student: " . $conn->error;
    }
} else {
    header("Location: studentlist.php");
    exit;
}
?>
