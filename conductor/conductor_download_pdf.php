<?php
session_start();

if (!isset($_SESSION['conductor_logged_in']) || !isset($_SESSION['conductor_name'])) {
    header("Location: conductor_login.php");
    exit();
}

// Trick the PDF generator to treat conductor as driver
$_SESSION['driver_logged_in'] = true;
$_SESSION['driver_name'] = $_SESSION['conductor_name'];

header("Location: generate_passenger_pdf.php");
exit();
?>
