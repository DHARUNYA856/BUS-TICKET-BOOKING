<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login/Register</title>
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
      overflow: hidden;
    }

    #particles-js {
      position: absolute;
      width: 100%;
      height: 100%;
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      z-index: -1;
    }

    .container {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(255, 255, 255, 0.1);
      padding: 40px;
      border-radius: 15px;
      backdrop-filter: blur(10px);
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
      text-align: center;
      animation: zoomIn 1s ease forwards;
    }

    h1 {
      color: white;
      margin-bottom: 30px;
    }

    .btn {
      display: inline-block;
      padding: 12px 25px;
      margin: 10px;
      font-size: 16px;
      border: none;
      border-radius: 25px;
      background: #00c6ff;
      color: white;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .btn:hover {
      background: #0072ff;
    }

    @keyframes zoomIn {
      from {
        transform: translate(-50%, -50%) scale(0.5);
        opacity: 0;
      }
      to {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
      }
    }
  </style>
</head>
<body>

<div id="particles-js"></div>

<div class="container">
  <h1>Welcome to BusHumb! Your journey begins with just one click....

</h1>
  <form action="user_login.php" method="get">
    <button class="btn" type="submit">Login</button>
  </form>
  <form action="user_register.php" method="get">
    <button class="btn" type="submit">Register</button>
  </form>
</div>

<!-- particles.js CDN -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

<!-- particles config -->
<script>
particlesJS("particles-js", {
  "particles": {
    "number": {
      "value": 80
    },
    "size": {
      "value": 3
    },
    "color": {
      "value": "#ffffff"
    },
    "line_linked": {
      "enable": true,
      "distance": 150,
      "color": "#ffffff",
      "opacity": 0.4,
      "width": 1
    },
    "move": {
      "enable": true,
      "speed": 4
    }
  }
});
</script>

</body>
</html>
