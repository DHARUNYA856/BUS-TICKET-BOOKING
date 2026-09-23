<?php 
include('../includes/db_connect.php'); 

function sendSMS($phone, $message) {
    echo "<script>alert('SMS Sent to $phone: $message');</script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $name = mysqli_real_escape_string($conn, $_POST['conductor_name']); 
    $phone = mysqli_real_escape_string($conn, $_POST['phone_number']); 
    $bus_id = mysqli_real_escape_string($conn, $_POST['bus_id']);
    $journey_date = mysqli_real_escape_string($conn, $_POST['journey_date']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
    $start_location = mysqli_real_escape_string($conn, $_POST['start_location']);
    $end_location = mysqli_real_escape_string($conn, $_POST['end_location']);

    // Updated SQL Query with journey_date
    $sql = "INSERT INTO conductor_details (conductor_name, phone_number, bus_id, journey_date, start_time, end_time, start_location, end_location) 
            VALUES ('$name', '$phone', '$bus_id', '$journey_date', '$start_time', '$end_time', '$start_location', '$end_location')";

    if (mysqli_query($conn, $sql)) { 
        $busResult = mysqli_query($conn, "SELECT bus_name FROM bus_details WHERE bus_id = '$bus_id'");
        $busRow = mysqli_fetch_assoc($busResult);
        $bus_name = $busRow['bus_name'];

        // Optional: Send an SMS (commented out here)
        // $message = "Hello $name, You are assigned to Bus $bus_name. Route: $start_location to $end_location, $start_time to $end_time.";
        // sendSMS($phone, $message);

        echo "<script>alert('Conductor added successfully!'); window.location.href='view_details.php';</script>"; 
    } else { 
        echo "Error: " . mysqli_error($conn); 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Conductor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: auto;
            position: relative;
            background-image: url('../images/admin2.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 20px;
        }

        .background-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        .form-card {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            animation: formJump 1s ease-in-out;
            position: relative;
        }

        @keyframes formJump {
            0% { transform: translateY(30px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0); }
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        input[type="text"],
        input[type="time"],
        input[type="date"],
        input[type="number"],
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        input:focus,
        select:focus {
            border-color: #007cf0;
            outline: none;
        }

        input[type="submit"] {
            background-color: #007cf0;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #005bb5;
        }
    </style>
</head>
<body>

<!-- Background Overlay -->
<div class="background-overlay"></div>

<!-- Form Container -->
<div class="form-card">
    <h2>Add Conductor</h2>
    <form method="POST">
        <label>Conductor Name:</label>
        <input type="text" name="conductor_name" required>

        <label>Phone Number:</label>
        <input type="text" name="phone_number" required>

        <label>Assigned Bus:</label>
        <select name="bus_id" required>
            <option value="">Select Bus</option>
            <?php
            $busQuery = "SELECT bus_id, bus_name FROM bus_details";
            $busResult = mysqli_query($conn, $busQuery);
            while ($row = mysqli_fetch_assoc($busResult)) {
                echo "<option value='{$row['bus_id']}'>{$row['bus_name']}</option>";
            }
            ?>
        </select>

        <label>Journey Date:</label>
        <input type="date" name="journey_date" required>

        <label>Start Time:</label>
        <input type="time" name="start_time" required>

        <label>End Time:</label>
        <input type="time" name="end_time" required>

        <label>Start Location:</label>
        <input type="text" name="start_location" required>

        <label>End Location:</label>
        <input type="text" name="end_location" required>

        <input type="submit" value="Add Conductor">
    </form>
</div>

</body>
</html>
