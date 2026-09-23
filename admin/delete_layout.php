<?php
include('../includes/db_connect.php');

if (isset($_GET['bus_id'])) {
    $bus_id = $_GET['bus_id'];

    // Delete seat layout
    $delete_query = "DELETE FROM seat_layout WHERE bus_id = '$bus_id'";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: add_seat_layout.php?deleted=1");
        exit;
    } else {
        echo "Error deleting layout: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
