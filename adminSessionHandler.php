<?php
session_start();

// Check user is logged in through session or cookie, will be redirected if not
if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
    header("Location: signIn.php"); 
    exit();
}

// Get the admin's name from the session
$adminName = isset($_SESSION['adminName']) ? htmlspecialchars($_SESSION['adminName']) : 'Admin';

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    session_unset();
    setcookie("email", "", time() - 3600, "/"); // Clear the email cookie
    header("Location: signIn.php"); 
    exit();
}
?>
