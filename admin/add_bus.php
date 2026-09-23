<?php
include '../includes/db_connect.php'; // Database Connection

// Handle form submission to add a new bus
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bus_name = $conn->real_escape_string($_POST['bus_name']);
    $bus_type = $conn->real_escape_string($_POST['bus_type']);
    $total_seats = (int) $_POST['total_seats'];
    $price_per_seat = (float) $_POST['price_per_seat'];
    $facilities = $conn->real_escape_string($_POST['facilities']);
    $rating = 0.0; // Default rating

    // Insert query
    $sql = "INSERT INTO bus_details (bus_name, bus_type, total_seats, price_per_seat, facilities, rating, created_at, updated_at) 
            VALUES ('$bus_name', '$bus_type', '$total_seats', '$price_per_seat', '$facilities', '$rating', NOW(), NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Bus added successfully!'); window.location.href='view_details.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Bus</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('../images/admin2.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
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
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        input:focus,
        textarea:focus,
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

<div class="background-overlay"></div>

<div class="form-card">
    <h2>Add Bus</h2>
    <form method="POST" action="">
        <label>Bus Name:</label>
        <input type="text" name="bus_name" required>

        <label>Bus Type:</label>
        <select name="bus_type" required>
            <option value="AC">AC</option>
            <option value="Non-AC">Non-AC</option>
            <option value="Sleeper">Sleeper</option>
            <option value="Seater">Seater</option>
        </select>

        <label>Total Seats:</label>
        <input type="number" name="total_seats" required>

        <label>Price Per Seat:</label>
        <input type="number" name="price_per_seat" step="0.01" required>

        <label>Facilities:</label>
        <textarea name="facilities" required></textarea>

        <input type="submit" value="Add Bus">
    </form>
</div>

</body>
</html>
