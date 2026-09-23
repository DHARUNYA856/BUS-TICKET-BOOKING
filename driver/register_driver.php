<?php
session_start();
include '../includes/db_connect.php';

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $driver_name = trim($_POST['driver_name']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if the driver's name exists in the driver_approved table (admin approval)
    $check_approved = "SELECT id FROM driver_approved WHERE driver_name = ?";
    $stmt_approved = mysqli_prepare($conn, $check_approved);
    mysqli_stmt_bind_param($stmt_approved, "s", $driver_name);
    mysqli_stmt_execute($stmt_approved);
    mysqli_stmt_store_result($stmt_approved);

    // If driver name is not approved by admin, show an error
    if (mysqli_stmt_num_rows($stmt_approved) == 0) {
        $error = "You are not eligible to register as a driver. Please contact admin for approval.";
    } else {
        // Check if the driver is already registered
        $check_registered = "SELECT driver_id FROM driver_register WHERE driver_name = ?";
        $stmt_registered = mysqli_prepare($conn, $check_registered);
        mysqli_stmt_bind_param($stmt_registered, "s", $driver_name);
        mysqli_stmt_execute($stmt_registered);
        mysqli_stmt_store_result($stmt_registered);

        // If the driver is already registered, show an error
        if (mysqli_stmt_num_rows($stmt_registered) > 0) {
            $error = "Driver name already registered!";
        } else {
            // Insert the new driver into the driver_register table
            $insert = "INSERT INTO driver_register (driver_name, password) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $insert);
            mysqli_stmt_bind_param($stmt, "ss", $driver_name, $password);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['registration_success'] = "Registration successful!";
                header("Location: driver_login.php");
                exit();
            } else {
                $error = "Error inserting: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($stmt_registered);
    }

    mysqli_stmt_close($stmt_approved);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            padding: 20px;
        }

        .form-container {
            max-width: 400px;
            margin: auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            color: #28a745;
            text-decoration: none;
            text-align: center;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Driver Registration</h2>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <input type="text" name="driver_name" placeholder="Driver Name" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>

        <p>Already registered? <a href="driver_login.php">Login here</a></p>
    </div>
</body>
</html>
