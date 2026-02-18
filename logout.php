<?php
session_start();

// Logout user
unset($_SESSION['user_id']);
unset($_SESSION['user_email']);
unset($_SESSION['user_name']);
unset($_SESSION['logged_in']);

session_destroy();

header('Location: index.php');
exit;
?>
