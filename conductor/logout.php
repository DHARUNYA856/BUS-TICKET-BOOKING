<?php
session_start();

// Destroy all session variables
session_unset();

// Destroy the session itself
session_destroy();

// Redirect to bus_ticket/index.php
header("Location: /bus_ticket/index.php");  // Adjust path if needed
exit();
?>
