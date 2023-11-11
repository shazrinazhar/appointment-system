<?php
session_start();
include_once "database.php";

// Check if the 'user' key exists in the session
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user);
    $stmt->execute();

    $readrow = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($readrow) {
        $user = $readrow['user_id'];
        $email = $readrow['email'];
        $password = $readrow['password'];
        $role = $readrow['role'];
        $full_name = $readrow['full_name'];
        $contact = $readrow['contact'];
    } else {
        // Handle the case where the user doesn't exist in the database
        // For example, you can redirect the user to the login page
        header("location: login.php");
        exit();
    }
} else {
    // Handle the case where the 'user' key is not set in the session
    // For example, you can redirect the user to the login page
    header("location: login.php");
    exit();
}
?>
