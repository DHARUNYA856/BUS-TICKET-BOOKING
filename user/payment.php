<?php
include('../includes/db_connect.php');
session_start();

if (!isset($_SESSION['passenger_details'])) {
    die("No passenger session found.");
}

$details = $_SESSION['passenger_details'];
$bus_id = $details['bus_id'];
$seat_no = $details['seat_no'];
$passenger_name = $details['passenger_name'];
$total_price = $details['total_price'];
$bus_name = $details['bus_name'];
$gender = $details['gender'];
$age = $details['age'];
$boarding_point = $details['boarding_point'];
$dropping_point = $details['dropping_point'];
$free_cancellation = $details['free_cancellation'];
$whatsapp_number = $details['whatsapp_number'];

$correct_upi = "rathinambal1980-1@okhdfcbank";
$success = false;
$error = "";

// Check if payment was already approved
$status_check = mysqli_query($conn, "SELECT payment_status FROM passenger_details 
                                   WHERE bus_id = '$bus_id' 
                                   AND seat_no = '$seat_no' 
                                   AND passenger_name = '$passenger_name'");
$status_row = mysqli_fetch_assoc($status_check);

if ($status_row && $status_row['payment_status'] === 'Paid') {
    header("Location: payment_success.php");
    exit();
} elseif ($status_row && $status_row['payment_status'] === 'Rejected') {
    $error = "❌ Your payment was rejected. Please contact support.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = uniqid("TXN");

    if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === 0) {
        $original_filename = strtoupper($_FILES['screenshot']['name']);

        if (strpos($original_filename, "RATHINAMBAL T") === false) {
            $error = "❌ Screenshot filename must contain 'RATHINAMBAL T'";
        } else {
            $upload_dir = "../images/uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $filename = uniqid() . '_' . basename($_FILES['screenshot']['name']);
            $target_path = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $target_path)) {
                $relative_path = "images/uploads/" . $filename;

                $sql = "UPDATE passenger_details SET 
                        payment_status = 'Pending', 
                        amount_paid = '$total_price', 
                        screenshot_path = '$relative_path',
                        transaction_id = '$transaction_id',
                        upi_id_used = '$correct_upi',
                        created_at = NOW()
                    WHERE bus_id = '$bus_id' 
                      AND seat_no = '$seat_no' 
                      AND passenger_name = '$passenger_name'";

                if (mysqli_query($conn, $sql)) {
                    $success = true;
                    $_SESSION['current_transaction'] = $transaction_id;
                } else {
                    $error = "❌ Database error: " . mysqli_error($conn);
                }
            } else {
                $error = "❌ Failed to upload file. Check folder permissions.";
            }
        }
    } else {
        $error = "❌ No screenshot uploaded or upload error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Payment Screenshot</title>
    <style>
        body {
            font-family: Arial;
            background: #f9f9f9;
            padding: 40px;
        }
        .container {
            width: 400px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 5px 10px rgba(0,0,0,0.1);
        }
        input[type="file"], button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
        }
        button {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            border: none;
        }
        button:hover {
            background-color: #218838;
        }
        .info {
            background: #e6ffe6;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #2ecc71;
        }
        .error, .success, .pending {
            padding: 10px;
            margin: 10px 0;
            font-weight: bold;
        }
        .error {
            background: #ffe6e6;
            border-left: 4px solid red;
            color: #a94442;
        }
        .success {
            background: #e6ffed;
            border-left: 4px solid green;
            color: #2e7d32;
        }
        .pending {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        img#preview {
            max-width: 100%;
            display: none;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        #statusChecker {
            display: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Submit Payment Screenshot</h2>

    <div class="info">
        <p><strong>Passenger Name:</strong> <?= htmlspecialchars($passenger_name) ?></p>
        <p><strong>Gender:</strong> <?= htmlspecialchars($gender) ?></p>
        <p><strong>Age:</strong> <?= htmlspecialchars($age) ?></p>
        <p><strong>Seat Number:</strong> <?= htmlspecialchars($seat_no) ?></p>
        <p><strong>Bus ID:</strong> <?= htmlspecialchars($bus_id) ?></p>
        <p><strong>Bus Name:</strong> <?= htmlspecialchars($bus_name) ?></p>
        <p><strong>Boarding Point:</strong> <?= htmlspecialchars($boarding_point) ?></p>
        <p><strong>Dropping Point:</strong> <?= htmlspecialchars($dropping_point) ?></p>
        <p><strong>Free Cancellation:</strong> <?= htmlspecialchars($free_cancellation) ?></p>
        <p><strong>WhatsApp Number:</strong> <?= htmlspecialchars($whatsapp_number) ?></p>
        <hr>
        <p><strong>Amount to pay:</strong> ₹<?= htmlspecialchars($total_price) ?></p>
        <p><strong>Recipient Name:</strong> RATHINAMBAL T</p>
        <p><strong>UPI ID:</strong> <?= $correct_upi ?></p>
        <p><small>Note: Screenshot file name must contain <strong>RATHINAMBAL T</strong></small></p>
    </div>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="pending" id="pendingMessage">
            ⌛ Payment submitted for verification. Checking status...
        </div>
        <div id="statusChecker"></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" enctype="multipart/form-data">
        <label>Upload Screenshot:</label>
        <input type="file" name="screenshot" accept="image/*" onchange="previewImage(event)" required>
        <img id="preview" src="#" alt="Preview">
        <button type="submit">Submit Payment Proof</button>
    </form>
    <?php endif; ?>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}

<?php if ($success): ?>
function checkPaymentStatus() {
    const statusDiv = document.getElementById('statusChecker');
    const pendingMsg = document.getElementById('pendingMessage');
    
    fetch('check_payment_status.php?transaction_id=<?= $transaction_id ?>')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'Paid') {
                window.location.href = 'payment_success.php';
            } else if (data.status === 'Rejected') {
                pendingMsg.innerHTML = "❌ Payment was rejected. Please contact support.";
                pendingMsg.className = "error";
            } else {
                setTimeout(checkPaymentStatus, 5000);
            }
        });
}
setTimeout(checkPaymentStatus, 5000);
<?php endif; ?>
</script>
</body>
</html>
