<?php
include('../includes/db_connect.php');

// Handle seat layout insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $bus_id = $_POST['bus_id'];
    $rows = $_POST['rows'];
    $columns = $_POST['columns'];

    $query = "INSERT INTO seat_layout (bus_id, total_rows, total_columns) VALUES ('$bus_id', '$rows', '$columns')";
    if (mysqli_query($conn, $query)) {
        header("Location: ../user/select_seat.php?bus_id=$bus_id");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle layout deletion
if (isset($_GET['delete_layout'])) {
    $bus_id = $_GET['delete_layout'];
    $deleteQuery = "DELETE FROM seat_layout WHERE bus_id = '$bus_id'";
    if (mysqli_query($conn, $deleteQuery)) {
        echo "<script>alert('Seat layout deleted successfully!'); window.location.href='add_seat_layout.php';</script>";
    } else {
        echo "Error deleting layout: " . mysqli_error($conn);
    }
}

$buses = mysqli_query($conn, "SELECT * FROM bus_details");
$layouts = mysqli_query($conn, "SELECT * FROM seat_layout");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Seat Layout</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            background-image: url('../images/admin2.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: auto;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
            animation: fadeIn 1s ease-in-out;
        }

        form {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            animation: formJump 1s ease-in-out;
        }

        @keyframes formJump {
            0% { transform: translateY(30px); opacity: 0; }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0); opacity: 1; }
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        input[type="number"],
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        input[type="number"]:focus,
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

        table {
            width: 80%;
            margin-top: 40px;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            overflow: hidden;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #007cf0;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f4f4f4;
        }

        a {
            color: #d9534f;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Add Seat Layout</h2>
    <form method="POST">
        <label>Bus:</label>
        <select name="bus_id" required>
            <option value="">Select Bus</option>
            <?php while ($bus = mysqli_fetch_assoc($buses)): ?>
                <option value="<?= $bus['bus_id'] ?>"><?= $bus['bus_name'] ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Rows:</label>
        <input type="number" name="rows" required><br><br>

        <label>Columns:</label>
        <input type="number" name="columns" required><br><br>

        <input type="submit" name="create" value="Create Layout">
    </form>

    <h2>Existing Seat Layouts</h2>
    <table>
        <tr>
            <th>Bus ID</th>
            <th>Rows</th>
            <th>Columns</th>
            <th>Action</th>
        </tr>
        <?php while ($layout = mysqli_fetch_assoc($layouts)): ?>
            <tr>
                <td><?= $layout['bus_id'] ?></td>
                <td><?= $layout['total_rows'] ?></td>
                <td><?= $layout['total_columns'] ?></td>
                <td>
                    <a href="?delete_layout=<?= $layout['bus_id'] ?>" onclick="return confirm('Delete layout?')">Delete Layout</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
