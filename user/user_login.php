<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "bus_ticket");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $query  = "SELECT * FROM user_register WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            echo "<script>alert('Login successful!'); window.location.href='search_bus.php';</script>";
        } else {
            echo "<script>alert('Incorrect password!');</script>";
        }
    } else {
        echo "<script>alert('User not registered!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      box-sizing: border-box;
    }

    body, html {
      height: 100%;
      overflow-x: hidden;
    }

    /* Parallax Background */
    .parallax {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url('../images/userlogin.jpeg'); /* Replace with actual image */
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      background-repeat: no-repeat;
      z-index: -1;
      opacity: 0.5;
      filter: brightness(0.7);
    }

    /* Floating elements effect */
    .floating-text {
      font-size: 2rem;
      color: white;
      position: absolute;
      top: 20%;
      left: 50%;
      transform: translate(-50%, -50%);
      animation: jump 3s ease-in-out infinite;
    }

    @keyframes jump {
      0%   { transform: translate(-50%, -50%) scale(1); }
      30%  { transform: translate(-50%, -70%) scale(1.1); }
      50%  { transform: translate(-50%, -50%) scale(1); }
      70%  { transform: translate(-50%, -60%) scale(1.05); }
      100% { transform: translate(-50%, -50%) scale(1); }
    }

    /* Login Box Styling */
    .login-box {
      position: absolute;
      top: 55%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(255, 255, 255, 0.75);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
      width: 320px;
      text-align: center;
    }

    .login-box h2 {
      color: #333;
      margin-bottom: 20px;
    }

    .login-box input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 8px;
      outline: none;
    }

    .login-box button {
      width: 100%;
      padding: 12px;
      background: #28a745;
      border: none;
      border-radius: 25px;
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .login-box button:hover {
      background: #218838;
    }
  </style>
</head>
<body>

<!-- Background -->
<div class="parallax"></div>

<!-- Floating Welcome Text -->
<div class="floating-text">
  <h2>Welcome to BusHumb</h2>
</div>

<!-- Login Box -->
<div class="login-box">
  <h2>User Login</h2>
  <form method="post" action="">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
</div>

</body>
</html>
