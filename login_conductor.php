<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conductor_name = mysqli_real_escape_string($conn, $_POST['conductor_name']);
    $password = $_POST['password'];

    $query = "SELECT * FROM conductor_details WHERE conductor_name = '$conductor_name'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Check if password is correct
        if (password_verify($password, $row['password'])) {
            $_SESSION['conductor_logged_in'] = $row['conductor_id'];
            $_SESSION['conductor_name'] = $row['conductor_name'];
            header("Location: conductor/conductor_dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Invalid Conductor Name!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Conductor Login</title>
</head>
<body>
    <h2>Conductor Login</h2>
    <form method="POST">
        <label>Conductor Name:</label>
        <input type="text" name="conductor_name" placeholder="Enter Conductor Name" required><br>

        <label>Password:</label>
        <input type="password" name="password" placeholder="Enter Password" required><br>

        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
</body>
</html>
