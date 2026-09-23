<?php
include 'includes/db_connect.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password for security

    // Insert new admin into database
    $sql = "INSERT INTO admin (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo "Admin inserted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Insert Admin</title>
</head>
<body>
    <h2>Add New Admin</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="New Admin Username" required>
        <br><br>
        <input type="password" name="password" placeholder="New Admin Password" required>
        <br><br>
        <input type="submit" value="Add Admin">
    </form>
</body>
</html>
