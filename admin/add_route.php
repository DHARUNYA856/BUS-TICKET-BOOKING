<?php
include('../includes/db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $route_name = mysqli_real_escape_string($conn, $_POST['route_name']);
    $start_point = mysqli_real_escape_string($conn, $_POST['start_point']);
    $end_point = mysqli_real_escape_string($conn, $_POST['end_point']);
    $distance_km = mysqli_real_escape_string($conn, $_POST['distance_km']);
    $estimated_time = mysqli_real_escape_string($conn, $_POST['estimated_time']);

    $sql = "INSERT INTO route_details (route_name, start_point, end_point, distance_km, estimated_time) 
            VALUES ('$route_name', '$start_point', '$end_point', '$distance_km', '$estimated_time')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Route added successfully!'); window.location.href='view_details.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Route</title>
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
        input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        input:focus {
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
    <h2>Add Route</h2>
    <form method="POST">
        <label>Route Name:</label>
        <input type="text" name="route_name" required>

        <label>Start Point:</label>
        <input type="text" name="start_point" required>

        <label>End Point:</label>
        <input type="text" name="end_point" required>

        <label>Distance (km):</label>
        <input type="number" step="0.01" name="distance_km" required>

        <label>Estimated Time:</label>
        <input type="time" name="estimated_time" required>

        <button type="submit">Add Route</button>
    </form>
</body>
</html>
