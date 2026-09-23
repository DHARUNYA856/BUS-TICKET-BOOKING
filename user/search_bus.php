<?php
// index.php - Front Page for Bus Ticket Search
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bus Ticket Search</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        html, body {
            height: 100%;
            overflow: hidden;
        }

        /* Animated Background */
        body {
            background: linear-gradient(45deg, #a1c4fd, #c2e9fb);
            animation: backgroundShift 10s ease infinite;
            background-size: 400% 400%;
        }

        @keyframes backgroundShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .search-form {
            max-width: 500px;
            margin: 100px auto;
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #0d47a1;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #0d47a1;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #1565c0;
        }
    </style>
</head>
<body>

    <div class="search-form">
        <h2>Connecting places, creating memories – Search Bus Tickets in BusHumb</h2>
        <form method="GET" action="view_details.php">
            <label>From:</label>
            <input type="text" name="origin" placeholder="Enter origin" required>
            <label>To:</label>
            <input type="text" name="destination" placeholder="Enter destination" required>
            <label>Journey Date:</label>
            <input type="date" name="journey_date" required>
            <button type="submit">Search</button>
        </form>
    </div>

</body>
</html>
