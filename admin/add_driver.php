<?php 
include('../includes/db_connect.php');  

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $driver_name = mysqli_real_escape_string($conn, $_POST['driver_name']);
    $license_number = mysqli_real_escape_string($conn, $_POST['license_number']);
    $bus_id = mysqli_real_escape_string($conn, $_POST['bus_id']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $start_location = mysqli_real_escape_string($conn, $_POST['start_location']);
    $end_location = mysqli_real_escape_string($conn, $_POST['end_location']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
    $journey_date = mysqli_real_escape_string($conn, $_POST['journey_date']);  // Journey Date

    $query = "INSERT INTO driver_details (driver_name, license_number, bus_id, phone_number, start_location, end_location, start_time, end_time, journey_date) 
              VALUES ('$driver_name', '$license_number', '$bus_id', '$phone_number', '$start_location', '$end_location', '$start_time', '$end_time', '$journey_date')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Driver added successfully!'); window.location.href='view_details.php';</script>";
    } else {
        echo "<script>alert('Error adding driver!');</script>";
    }
}

$busQuery = "SELECT bus_id, bus_name FROM bus_details";
$busResult = mysqli_query($conn, $busQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Driver</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            background-image: url('../images/admin2.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: auto;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
            animation: fadeIn 1s ease-in-out;
        }

        form {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            animation: formJump 1s ease-in-out;
        }

        @keyframes formJump {
            0% { transform: translateY(30px); opacity: 0; }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0); opacity: 1; }
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

        button[type="submit"] {
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

        button[type="submit"]:hover {
            background-color: #005bb5;
        }
    </style>
</head>
<body>
    <h2>Add Driver</h2>
    <form action="" method="POST">
        <label for="driver_name">Driver Name:</label>
        <input type="text" name="driver_name" required>

        <label for="license_number">License Number:</label>
        <input type="text" name="license_number" required>

        <label for="bus_id">Select Bus:</label>
        <select name="bus_id" required>
            <option value="">-- Select Bus --</option>
            <?php while ($bus = mysqli_fetch_assoc($busResult)) { ?>
                <option value="<?php echo htmlspecialchars($bus['bus_id']); ?>">
                    <?php echo htmlspecialchars($bus['bus_name']); ?>
                </option>
            <?php } ?>
        </select>

        <label for="phone_number">Phone Number:</label>
        <input type="text" name="phone_number" required>

        <label for="journey_date">Journey Date:</label>
        <input type="date" name="journey_date" required>

        <label for="start_location">Start Location:</label>
        <input type="text" name="start_location" required>

        <label for="end_location">End Location:</label>
        <input type="text" name="end_location" required>

        <label for="start_time">Start Time:</label>
        <input type="time" name="start_time" required>

        <label for="end_time">End Time:</label>
        <input type="time" name="end_time" required>

        <button type="submit">Add Driver</button>
    </form>
</body>
</html>
