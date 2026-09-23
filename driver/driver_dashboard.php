<?php
session_start();
if (!isset($_SESSION['driver_logged_in']) || !isset($_SESSION['driver_name'])) {
    header("Location: driver_login.php");
    exit();
}

include '../includes/db_connect.php';
$driver_name = $_SESSION['driver_name'];

// Fetch driver details and bus assignment
$query = "SELECT d.*, b.bus_name FROM driver_details d  
          LEFT JOIN bus_details b ON d.bus_id = b.bus_id  
          WHERE d.driver_name = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $driver_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$details = mysqli_fetch_assoc($result);

$assigned_bus_name = $details['bus_name'] ?? '';

// PDF Export Functionality
if (isset($_GET['export_pdf'])) {
    $passenger_sql = "SELECT * FROM passenger_details WHERE bus_name = ? AND payment_status = 'Paid'";
    $passenger_stmt = mysqli_prepare($conn, $passenger_sql);
    mysqli_stmt_bind_param($passenger_stmt, "s", $assigned_bus_name);
    mysqli_stmt_execute($passenger_stmt);
    $passenger_result = mysqli_stmt_get_result($passenger_stmt);

    $pdf_content = "%PDF-1.4\n";
    $pdf_content .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
    $pdf_content .= "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
    $pdf_content .= "3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 4 0 R >> >> /MediaBox [0 0 612 792] /Contents 5 0 R >>\nendobj\n";
    $pdf_content .= "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

    $stream = "BT\n/F1 12 Tf\n50 750 Td\n(Passenger List - $driver_name) Tj\nET\n";
    $stream .= "BT\n/F1 10 Tf\n50 730 Td\n(Bus: ".($assigned_bus_name ?: 'N/A').") Tj\nET\n";
    $stream .= "BT\n50 715 Td\n(Route: ".($details['start_location'] ?? '')." to ".($details['end_location'] ?? '').") Tj\nET\n";

    $y = 690;
    $stream .= "BT\n50 $y Td\n(Name               Seat No   Age  Gender  Boarding        Dropping        Price) Tj\nET\n";
    $y -= 20;

    while ($row = mysqli_fetch_assoc($passenger_result)) {
        $line = sprintf("%-20s %-8s %-4s %-7s %-15s %-15s %s",
            substr($row['passenger_name'], 0, 20),
            $row['seat_no'],
            $row['age'],
            $row['gender'],
            substr($row['boarding_point'], 0, 15),
            substr($row['dropping_point'], 0, 15),
            $row['total_price']
        );
        $stream .= "BT\n50 $y Td\n($line) Tj\nET\n";
        $y -= 15;
    }

    $pdf_content .= "5 0 obj\n<< /Length " . strlen($stream) . " >>\nstream\n$stream\nendstream\nendobj\n";
    $pdf_content .= "xref\n0 6\n0000000000 65535 f \n0000000015 00000 n \n0000000077 00000 n \n0000000150 00000 n \n0000000275 00000 n \n0000000345 00000 n \n";
    $pdf_content .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n" . strlen($pdf_content) . "\n%%EOF";

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="passenger_list.pdf"');
    echo $pdf_content;
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Your CSS styles remain unchanged */
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(to right, #e0f2f1, #ffffff);
            min-height: 100vh;
        }
        h2 {
            text-align: center;
            color: #00695c;
            margin-bottom: 40px;
            font-size: 32px;
        }
        table {
            width: 95%;
            margin: auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 15px;
            text-align: center;
            font-size: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        th {
            background-color: #00796b;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #e0f2f1;
        }
        .logout-btn {
            display: block;
            width: 150px;
            padding: 10px;
            margin: 20px auto;
            background-color: #e74c3c;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }
        .pdf-btn {
            background-color: #d9534f;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 10px;
        }
    </style>
</head>
<body>

<h2>Welcome, <?= htmlspecialchars($driver_name); ?>!</h2>
<a href="logout.php" class="logout-btn">Logout</a>

<h2>Your Assigned Schedule</h2>

<?php if ($details): ?>
<table>
    <thead>
    <tr>
        <th>Driver ID</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Bus Name</th>
        <th>Start Location</th>
        <th>End Location</th>
        <th>Start Time</th>
        <th>End Time</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?= htmlspecialchars($details['driver_id']); ?></td>
        <td><?= htmlspecialchars($details['driver_name']); ?></td>
        <td><?= htmlspecialchars($details['phone_number']); ?></td>
        <td><?= htmlspecialchars($assigned_bus_name); ?></td>
        <td><?= htmlspecialchars($details['start_location']); ?></td>
        <td><?= htmlspecialchars($details['end_location']); ?></td>
        <td><?= htmlspecialchars($details['start_time']); ?></td>
        <td><?= htmlspecialchars($details['end_time']); ?></td>
    </tr>
    </tbody>
</table>
<?php else: ?>
<div class="no-schedule">Schedule is not updated yet!</div>
<?php endif; ?>

<h2>Passenger Details for Your Bus
    <a href="?export_pdf=1" class="pdf-btn">Download PDF</a>
</h2>

<?php
$passenger_sql = "SELECT * FROM passenger_details WHERE bus_name = ? AND payment_status = 'Paid'";
$passenger_stmt = mysqli_prepare($conn, $passenger_sql);
mysqli_stmt_bind_param($passenger_stmt, "s", $assigned_bus_name);
mysqli_stmt_execute($passenger_stmt);
$passenger_result = mysqli_stmt_get_result($passenger_stmt);

if (mysqli_num_rows($passenger_result) > 0): ?>
<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Seat No</th>
        <th>Bus Name</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Boarding</th>
        <th>Dropping</th>
        <th>Total Price</th>
        <th>Free Cancellation</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($row = mysqli_fetch_assoc($passenger_result)): ?>
        <tr>
            <td><?= htmlspecialchars($row['passenger_name']) ?></td>
            <td><?= htmlspecialchars($row['seat_no']) ?></td>
            <td><?= htmlspecialchars($row['bus_name']) ?></td>
            <td><?= htmlspecialchars($row['age']) ?></td>
            <td><?= htmlspecialchars($row['gender']) ?></td>
            <td><?= htmlspecialchars($row['boarding_point']) ?></td>
            <td><?= htmlspecialchars($row['dropping_point']) ?></td>
            <td><?= htmlspecialchars($row['total_price']) ?></td>
            <td><?= htmlspecialchars($row['free_cancellation']) ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<div class="no-schedule">No passengers booked yet!</div>
<?php endif; ?>

</body>
</html>
