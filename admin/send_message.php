<?php
if (isset($_POST['phone']) && isset($_POST['message'])) {
    $phone = preg_replace('/\D/', '', $_POST['phone']); // clean number
    $message = urlencode($_POST['message']);

    if (!empty($phone) && !empty($message)) {
        header("Location: https://wa.me/$phone?text=$message");
        exit;
    } else {
        header("Location: view_details.php?status=invalid");
        exit;
    }
} else {
    header("Location: view_details.php?status=failure");
    exit;
}
?>
