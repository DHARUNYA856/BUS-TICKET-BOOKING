<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bus Ticket Booking - Login</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Moment.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('images/download bus2.jpeg'); /* Update with your image path */
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            font-family: Arial, sans-serif;
            color: white;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            animation: fadeInBody 2s ease-in;
        }

        .container {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 40px;
            border-radius: 15px;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInContainer 1.5s forwards ease-in-out 0.5s;
        }

        h2, h3 {
            margin: 20px 0 10px;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 10px 0 20px;
        }

        li {
            margin: 10px 0;
        }

        a {
            display: inline-block;
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            background-color: rgba(0, 123, 255, 0.8);
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
        }

        a:hover {
            background-color: rgba(0, 123, 255, 1);
            transform: scale(1.05);
        }

        /* Animations */
        @keyframes fadeInBody {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInContainer {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome to BusHumb</h2>

        <h3>Login As:</h3>
        <ul>
            <li><a href="admin_login.php">Admin</a></li>
            <li><a href="driver/driver_login.php">Driver</a></li>
            <li><a href="conductor/conductor_login.php">Conductor</a></li>
        </ul>

        <h3>Register As:</h3>
        <ul>
            <li><a href="driver/register_driver.php">Register as Driver</a></li>
            <li><a href="conductor/register_conductor.php">Register as Conductor</a></li>
        </ul>
    </div>

    <!-- Optional: Moment.js usage example -->
    <script>
        const now = moment().format('LLLL');
        console.log("Current time:", now); // Useful for logs or time-restricted logic
    </script>
</body>
</html>
