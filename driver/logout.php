<?php
session_start();

// Destroy all session variables
session_unset();

// Destroy the session itself
session_destroy();

// Redirect to the login page or homepage
header("Location: /bus_ticket/index.php");  // Adjust this path if needed
exit();
?>
