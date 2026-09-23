<?php
include('../includes/db_connect.php');
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_id = $_POST['bus_id'];
    $route_id = $_POST['route_id'];
    $seat_no = $_POST['seat_no'];
    $bus_name = $_POST['bus_name']; // Now entered by user
    $boarding_point = $_POST['boarding_point'];
    $dropping_point = $_POST['dropping_point'];
    $passenger_name = $_POST['passenger_name'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $whatsapp_number = $_POST['whatsapp_number'];
    $free_cancellation = $_POST['free_cancellation'];
    $total_price = $_POST['total_price'];

    // Insert passenger details into the database
    $sql = "INSERT INTO passenger_details 
        (bus_id, route_id, seat_no, bus_name, boarding_point, dropping_point, passenger_name, gender, age, whatsapp_number, free_cancellation, total_price)
        VALUES 
        ('$bus_id', '$route_id', '$seat_no', '$bus_name', '$boarding_point', '$dropping_point', 
        '$passenger_name', '$gender', '$age', '$whatsapp_number', '$free_cancellation', '$total_price')";

    if (mysqli_query($conn, $sql)) {
        // Store passenger details in session
        $_SESSION['passenger_details'] = [
            'bus_id' => $bus_id,
            'bus_name' => $bus_name,
            'route_id' => $route_id,
            'seat_no' => $seat_no,
            'passenger_name' => $passenger_name,
            'gender' => $gender,
            'age' => $age,
            'whatsapp_number' => $whatsapp_number,
            'boarding_point' => $boarding_point,
            'dropping_point' => $dropping_point,
            'free_cancellation' => $free_cancellation,
            'total_price' => $total_price
        ];

        header('Location: payment.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Get data from GET parameters
$bus_id = $_GET['bus_id'] ?? '';
$seat_no = $_GET['seat_no'] ?? '';
$total_price = $_GET['total_price'] ?? '';
$route_id = $_GET['route_id'] ?? '';
$gender = $_GET['gender'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Passenger Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../images/passeng.jpeg');
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 40px 0;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.1);
            width: 400px;
            margin: auto;
            backdrop-filter: blur(4px);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        button {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Enter Passenger Details</h2>
    <form method="POST" action="passenger_details.php">
        <!-- Hidden Inputs -->
        <input type="hidden" name="bus_id" value="<?= htmlspecialchars($bus_id) ?>">
        <input type="hidden" name="seat_no" value="<?= htmlspecialchars($seat_no) ?>">
        <input type="hidden" name="route_id" value="<?= htmlspecialchars($route_id) ?>">
        <input type="hidden" name="total_price" value="<?= htmlspecialchars($total_price) ?>">
        <input type="hidden" name="gender" value="<?= htmlspecialchars($gender) ?>">

        <!-- Disabled Display Values -->
        <label>Bus ID</label>
        <input type="text" value="<?= htmlspecialchars($bus_id) ?>" disabled>

        <label for="bus_name">Bus Name</label>
        <input type="text" name="bus_name" required>

        <label>Seat No</label>
        <input type="text" value="<?= htmlspecialchars($seat_no) ?>" disabled>

        <label>Gender</label>
        <input type="text" value="<?= htmlspecialchars($gender) ?>" disabled>

        <!-- User Input Fields -->
        <label for="passenger_name">Passenger Name</label>
        <input type="text" name="passenger_name" required>

        <label for="age">Age</label>
        <input type="number" name="age" required>

        <label for="boarding_point">Boarding Point</label>
        <input type="text" name="boarding_point" required>

        <label for="dropping_point">Dropping Point</label>
        <input type="text" name="dropping_point" required>

        <label for="whatsapp_number">WhatsApp Number</label>
        <input type="text" name="whatsapp_number" required>

        <label for="free_cancellation">Free Cancellation</label>
        <select name="free_cancellation" required>
            <option value="">-- Select --</option>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
        </select>

        <button type="submit">Proceed to Payment</button>
    </form>
</div>
</body>
</html>