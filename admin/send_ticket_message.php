<?php
include('../includes/db_connect.php');

if (isset($_POST['transaction_id']) && isset($_POST['payment_action'])) {
    $transaction_id = mysqli_real_escape_string($conn, $_POST['transaction_id']);
    $payment_action = $_POST['payment_action'];

    $query = "SELECT * FROM passenger_details WHERE transaction_id = '$transaction_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $payment = mysqli_fetch_assoc($result);

        $passenger_name = $payment['passenger_name'];
        $age = $payment['age'];
        $gender = $payment['gender'];
        $boarding_point = $payment['boarding_point'];
        $dropping_point = $payment['dropping_point'];
        $bus_name = $payment['bus_name'];
        $whatsapp_number = $payment['whatsapp_number'];
        $amount_paid = $payment['amount_paid'];

        if ($payment_action == 'accept') {
            // Update payment status to Paid
            $update_query = "UPDATE passenger_details SET payment_status = 'Paid' WHERE transaction_id = '$transaction_id'";
            mysqli_query($conn, $update_query);

            // WhatsApp confirmation message
            $message = "🚍 *Booking Confirmed*\n\n";
            $message .= "Hello $passenger_name, your booking has been confirmed!\n\n";
            $message .= "*Bus*: $bus_name\n";
            $message .= "*passenger name*: $passenger_name\n";
            $message .= "*age*: $age\n";
            $message .= "*gender*: $gender\n";
            $message .= "*Boarding*: $boarding_point\n";
            $message .= "*Dropping*: $dropping_point\n";
            $message .= "*Amount Paid*: ₹$amount_paid\n\n";
            $message .= "Thank you for choosing BusHumb!";

        } elseif ($payment_action == 'reject') {
            // Delete the record
            $delete_query = "DELETE FROM passenger_details WHERE transaction_id = '$transaction_id'";
            mysqli_query($conn, $delete_query);

            // WhatsApp rejection message
            $message = "🚫 *Payment Rejected*\n\n";
            $message .= "Hi $passenger_name, your payment has been rejected. Please contact our support team for assistance.";
        }

        // Redirect to WhatsApp with the message
        $encoded_message = urlencode($message);
        $whatsapp_url = "https://wa.me/$whatsapp_number?text=$encoded_message";
        header("Location: $whatsapp_url");
        exit();

    } else {
        echo "Transaction not found.";
    }
}
?>
