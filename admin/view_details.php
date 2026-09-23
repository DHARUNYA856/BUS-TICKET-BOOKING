view_details.php<?php 
session_start();



include('../includes/db_connect.php');

// Handle Deletion using prepared statements
if (isset($_GET['delete_id']) && isset($_GET['table']) && isset($_GET['id_column'])) {
    $delete_id = $_GET['delete_id'];
    $table = $_GET['table'];
    $id_column = $_GET['id_column'];

    $allowed_tables = [
        'bus_details' => 'bus_id',
        'conductor_details' => 'conductor_id',
        'driver_details' => 'driver_id',
        'route_details' => 'route_id',
        'schedule_details' => 'schedule_id',
        'seats' => 'seat_id'
    ];

    if (array_key_exists($table, $allowed_tables) && $allowed_tables[$table] === $id_column) {
        // Delete related rows in seat_layout if bus is being deleted
if ($table === 'bus_details' && $id_column === 'bus_id') {
    $stmt_del = mysqli_prepare($conn, "DELETE FROM seat_layout WHERE bus_id = ?");
    mysqli_stmt_bind_param($stmt_del, "i", $delete_id);
    mysqli_stmt_execute($stmt_del);
    mysqli_stmt_close($stmt_del);
}

$deleteQuery = "DELETE FROM $table WHERE $id_column = ?";
$stmt = mysqli_prepare($conn, $deleteQuery);
mysqli_stmt_bind_param($stmt, "i", $delete_id);
mysqli_stmt_execute($stmt);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Record deleted successfully!'); window.location.href='view_details.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error deleting record: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Invalid deletion request!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Details</title>
    <style>
        .action-container {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .action-btn {
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .send-btn {
            background-color: green;
            color: white;
        }
        .delete-link {
            color: red;
            text-decoration: none;
            font-weight: bold;
        }
        table {
            margin-bottom: 30px;
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 8px;
            border: 1px solid #333;
        }

        /* Styling for logout button */
        .logout-btn {
            float: right;
            margin: 15px;
            background-color: #444;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>


<?php
if (isset($_GET['status'])) {
    $alerts = [
        'success' => '✅ Message sent!',
        'failure' => '❌ Failed to send message.',
        'invalid' => '⚠ Missing phone or message.',
    ];
    if (isset($alerts[$_GET['status']])) {
        echo "<script>alert('{$alerts[$_GET['status']]}');</script>";
    }
}
?>

<h2>Conductor Details</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Phone</th>
        <th>journey date</th>
        <th>Bus</th>
        <th>Start</th>
        <th>End</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Action</th>
    </tr>
    <?php
    $query = "SELECT c.*, b.bus_name FROM conductor_details c LEFT JOIN bus_details b ON c.bus_id = b.bus_id";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <tr>
            <td><?php echo htmlspecialchars($row['conductor_id']); ?></td>
            <td><?php echo htmlspecialchars($row['conductor_name']); ?></td>
            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
            <td><?php echo htmlspecialchars($row['journey_date']); ?></td>
            <td><?php echo htmlspecialchars($row['bus_name'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($row['start_location']); ?></td>
            <td><?php echo htmlspecialchars($row['end_location']); ?></td>
            <td><?php echo htmlspecialchars($row['start_time']); ?></td>
            <td><?php echo htmlspecialchars($row['end_time']); ?></td>
            <td>
                <div class="action-container">
                <a class="delete-link" href="?delete_id=<?= $row['conductor_id'] ?>&table=conductor_details&id_column=conductor_id" onclick="return confirm('Delete this record?')">Delete</a>
                <form action="send_message.php" method="POST">
                    <input type="hidden" name="phone" value="<?= $row['phone_number'] ?>">
                    <input type="hidden" name="message" value="Hi <?= $row['conductor_name'] ?>, your schedule for <?= $row['journey_date'] ?> has been updated.">
                    <button class="send-btn" type="submit">Send Message</button>
                </form>
            </div>
            </td>
        </tr>
    <?php } ?>
</table>

<h2>Driver Details</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>License</th>
        <th>Journey Date</th>
        <th>Phone</th>
        <th>Bus</th>
        <th>Start</th>
        <th>End</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Action</th>
    </tr>
    <?php
    $query = "SELECT d.*, b.bus_name FROM driver_details d LEFT JOIN bus_details b ON d.bus_id = b.bus_id";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <tr>
            <td><?php echo htmlspecialchars($row['driver_id']); ?></td>
            <td><?php echo htmlspecialchars($row['driver_name']); ?></td>
            <td><?php echo htmlspecialchars($row['license_number']); ?></td>
            <td><?php echo htmlspecialchars($row['journey_date']); ?></td>
            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
            <td><?php echo htmlspecialchars($row['bus_name'] ?? 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($row['start_location']); ?></td>
            <td><?php echo htmlspecialchars($row['end_location']); ?></td>
            <td><?php echo htmlspecialchars($row['start_time']); ?></td>
            <td><?php echo htmlspecialchars($row['end_time']); ?></td>
            <td>
               <div class="action-container">
                <a class="delete-link" href="?delete_id=<?= $row['driver_id'] ?>&table=driver_details&id_column=driver_id" onclick="return confirm('Delete this record?')">Delete</a>
                <form action="send_message.php" method="POST">
                    <input type="hidden" name="phone" value="<?= $row['phone_number'] ?>">
                    <input type="hidden" name="message" value="Hi <?= $row['driver_name'] ?>, please review your schedule for <?= $row['journey_date'] ?>.">
                    <button class="send-btn" type="submit">Send Message</button>
                </form>
            </div> 
            </td>
        </tr>
    <?php } ?>
</table>

<?php
function displayTable($conn, $table, $columns, $id_column) {
    echo "<h2>" . ucfirst(str_replace("_", " ", $table)) . "</h2>";
    $result = mysqli_query($conn, "SELECT * FROM $table");
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table><tr>";
        foreach ($columns as $col) echo "<th>" . ucfirst(str_replace("_", " ", $col)) . "</th>";
        echo "<th>Action</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($columns as $col) {
                echo "<td>" . htmlspecialchars($row[$col] ?? 'N/A') . "</td>";
            }
            echo "<td><a href='view_details.php?delete_id=" . $row[$id_column] . "&table=$table&id_column=$id_column'
                       onclick=\"return confirm('Delete this record?')\" class='delete-link'>Delete</a></td></tr>";
        }
        echo "</table><br>";
    } else {
        echo "<p>No records found in $table.</p>";
    }
}

displayTable($conn, 'bus_details', ['bus_id', 'bus_name', 'bus_type', 'total_seats', 'price_per_seat', 'facilities'], 'bus_id');
displayTable($conn, 'route_details', ['route_id', 'route_name', 'start_point', 'end_point', 'distance_km', 'estimated_time'], 'route_id');
displayTable($conn, 'schedule_details', ['schedule_id', 'bus_id', 'route_name', 'journey_date', 'departure_time', 'arrival_time'], 'schedule_id');

?>

</body>
</html>