<?php
session_start();
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conductor_name = trim($_POST['conductor_name']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if the conductor is approved by the admin
    $check_approved_query = "SELECT id FROM conductor_approved WHERE conductor_name = ?";
    $check_approved_stmt = mysqli_prepare($conn, $check_approved_query);
    mysqli_stmt_bind_param($check_approved_stmt, "s", $conductor_name);
    mysqli_stmt_execute($check_approved_stmt);
    mysqli_stmt_store_result($check_approved_stmt);

    // If conductor is not approved, show an error
    if (mysqli_stmt_num_rows($check_approved_stmt) == 0) {
        $error = "You are not eligible to register as a conductor. Please contact the admin for approval.";
    } else {
        // Check if the conductor already exists in the conductor_register table
        $check_query = "SELECT conductor_id FROM conductor_register WHERE conductor_name = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "s", $conductor_name);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = "Conductor name already exists!";
        } else {
            // Insert new conductor into the conductor_register table
            $insert_query = "INSERT INTO conductor_register (conductor_name, password) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "ss", $conductor_name, $password);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['registration_success'] = "Registration successful!";
                header("Location: conductor_login.php");
                exit();
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($check_stmt);
    }

    mysqli_stmt_close($check_approved_stmt);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register as a Conductor</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('../images/conductor.jpeg'); /* Set the background image */
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-box {
            background-color: rgba(255, 255, 255, 0.85); /* Semi-transparent white background */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: jumpIn 1s ease;
        }

        @keyframes jumpIn {
            0% {
                transform: translateY(100px);
                opacity: 0;
            }
            50% {
                transform: translateY(-30px);
                opacity: 0.7;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
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
        }

        a {
            display: inline-block;
            margin-top: 15px;
            color: #28a745;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>Conductor Registration</h2>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <input type="text" name="conductor_name" placeholder="Name" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>

        <p>Already registered? <a href="conductor_login.php">Login here</a></p>
    </div>
</body>
</html>
