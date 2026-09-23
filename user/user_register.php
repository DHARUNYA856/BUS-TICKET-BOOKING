<?php
$conn = mysqli_connect("localhost", "root", "", "bus_ticket");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if user already registered
    $check = mysqli_query($conn, "SELECT * FROM user_register WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('User already registered!');</script>";
    } else {
        $query = "INSERT INTO user_register (name, email, phone, password) 
                  VALUES ('$name', '$email', '$phone', '$password')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Registration successful!');window.location.href='user_login.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Registration</title>
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

    .parallax {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url('../images/userlogin.jpeg'); /* Replace with your image */
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      background-repeat: no-repeat;
      z-index: -1;
      opacity: 0.5;
      filter: brightness(0.7);
    }

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

    .register-box {
      position: absolute;
      top: 55%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(255, 255, 255, 0.75);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
      width: 340px;
      text-align: center;
    }

    .register-box h2 {
      color: #333;
      margin-bottom: 20px;
    }

    .register-box input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 8px;
      outline: none;
    }

    .register-box button {
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

    .register-box button:hover {
      background: #218838;
    }
  </style>
</head>
<body>

<!-- Background -->
<div class="parallax"></div>

<!-- Floating Text -->
<div class="floating-text">
  <h2>Join Our BusHumb</h2>
</div>

<!-- Registration Form -->
<div class="register-box">
  <h2>User Registration</h2>
  <form method="post">
    <input type="text" name="name" placeholder="Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="phone" placeholder="Phone" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
  </form>
</div>

</body>
</html>
