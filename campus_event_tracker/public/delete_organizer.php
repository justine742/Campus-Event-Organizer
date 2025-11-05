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

    // Delete the organizer
    $deleteQuery = "DELETE FROM users WHERE username='$username' AND role='organizer'";
    if ($conn->query($deleteQuery)) {
        header("Location: organizerlist.php?success=Organizer+removed+successfully");
        exit;
    } else {
        echo "Error removing organizer: " . $conn->error;
    }
} else {
    header("Location: organizerlist.php");
    exit;
}
?>
