<?php
session_start();

// Destroy the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session itself

// Redirect to the login page
header("Location: ../main/main_page.html");
exit();
?>
