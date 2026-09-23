<?php
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bus_id = mysqli_real_escape_string($conn, $_POST['bus_id']);
    $route_name = mysqli_real_escape_string($conn, $_POST['route_name']);
    $departure_time = mysqli_real_escape_string($conn, $_POST['departure_time']);
    $arrival_time = mysqli_real_escape_string($conn, $_POST['arrival_time']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $available_seats = mysqli_real_escape_string($conn, $_POST['available_seats']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $journey_date = mysqli_real_escape_string($conn, $_POST['journey_date']);

    $sql = "INSERT INTO schedule_details (bus_id, route_name, departure_time, arrival_time, status, available_seats, price, journey_date)
            VALUES ('$bus_id', '$route_name', '$departure_time', '$arrival_time', '$status', '$available_seats', '$price', '$journey_date')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Schedule added successfully!'); window.location.href='view_details.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// Fetch buses
$busResult = mysqli_query($conn, "SELECT bus_id, bus_name FROM bus_details");

// Fetch routes
$routeResult = mysqli_query($conn, "SELECT route_name FROM route_details");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Schedule</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            max-width: 600px;
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
        input[type="number"],
        input[type="date"],
        input[type="datetime-local"],
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
    <h2>Add Schedule</h2>
    <form method="post" action="">
        <label>Bus:</label>
        <select name="bus_id" required>
            <option value="">Select Bus</option>
            <?php while ($bus = mysqli_fetch_assoc($busResult)) {
                echo "<option value='{$bus['bus_id']}'>" . htmlspecialchars($bus['bus_name']) . "</option>";
            } ?>
        </select>

        <label>Route:</label>
        <select name="route_name" required>
            <option value="">Select Route</option>
            <?php while ($route = mysqli_fetch_assoc($routeResult)) {
                echo "<option value='" . htmlspecialchars($route['route_name']) . "'>" . htmlspecialchars($route['route_name']) . "</option>";
            } ?>
        </select>

        <label>Journey Date:</label>
        <input type="date" name="journey_date" required>

        <label>Departure Time:</label>
        <input type="datetime-local" name="departure_time" required>

        <label>Arrival Time:</label>
        <input type="datetime-local" name="arrival_time" required>

        <label>Status:</label>
        <select name="status" required>
            <option value="Scheduled">Scheduled</option>
            <option value="Delayed">Delayed</option>
            <option value="Cancelled">Cancelled</option>
        </select>

        <label>Available Seats:</label>
        <input type="number" name="available_seats" required>

        <label>Price Per Seat:</label>
        <input type="number" step="0.01" name="price" required>

        <input type="submit" value="Add Schedule">
    </form>
</body>
</html>
