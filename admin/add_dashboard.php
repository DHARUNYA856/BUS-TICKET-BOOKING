<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login_admin.php");
    exit();
}

include('../includes/db_connect.php');

// Handle new driver/conductor form submissions
$driver_msg = $conductor_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_driver']) && !empty(trim($_POST['driver_name']))) {
        $driver_name = mysqli_real_escape_string($conn, $_POST['driver_name']);
        mysqli_query($conn, "INSERT INTO driver_approved (driver_name) VALUES ('$driver_name')");
        $driver_msg = "Driver '$driver_name' added successfully.";
    }

    if (isset($_POST['add_conductor']) && !empty(trim($_POST['conductor_name']))) {
        $conductor_name = mysqli_real_escape_string($conn, $_POST['conductor_name']);
        mysqli_query($conn, "INSERT INTO conductor_approved (conductor_name) VALUES ('$conductor_name')");
        $conductor_msg = "Conductor '$conductor_name' added successfully.";
    }
}

// Get pending payments
$pending_payments = mysqli_query($conn, "SELECT * FROM passenger_details WHERE payment_status = 'Pending'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: #4B49AC; color: white; padding-top: 30px; display: flex; flex-direction: column; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; }
        .sidebar a { padding: 15px 20px; text-decoration: none; color: white; font-weight: bold; transition: background 0.3s; }
        .sidebar a:hover { background-color: #3733a3; }
        .logout { margin-top: auto; background: #e63946 !important; }

        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .card-container { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px; }
        .card { flex: 1; min-width: 200px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center; cursor: pointer; }
        .payment-section { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-top: 20px; }
        .payment-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .payment-table th, .payment-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .payment-table th { background-color: #f2f2f2; }
        .payment-preview { max-width: 150px; max-height: 100px; cursor: pointer; }
        .accept-btn { background-color: #28a745; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .reject-btn { background-color: #dc3545; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            overflow: auto;
            padding-top: 60px;
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .modal input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .modal button {
            background-color: #007bff; /* Blue color for Add Driver and Add Conductor button */
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        .modal button:hover {
            background-color: #0056b3; /* Darker blue shade on hover */
        }

        /* Button colors for Add Driver and Add Conductor */
        .toggle-btn {
            background-color: #007bff; /* Blue button color */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .toggle-btn:hover {
            background-color: #0056b3; /* Darker blue shade on hover */
        }

        .toggle-btn:active {
            background-color: #004085; /* Even darker shade when clicked */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="add_bus.php">+ Add Bus</a>
        <a href="add_route.php">+ Add Route</a>
        <a href="add_schedule.php">+ Add Schedule</a>
        <a href="add_driver.php">+ Add Driver</a>
        <a href="add_conductor.php">+ Add Conductor</a>
        <a href="add_seat_layout.php">+ Add Seat Layout</a>
        <a href="view_details.php">View Details</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="main-content">
        <h1>Welcome to BusHumb, Admin!</h1>

        <?php if (!empty($driver_msg)): ?>
            <div class="message success"><?= $driver_msg ?></div>
        <?php endif; ?>

        <?php if (!empty($conductor_msg)): ?>
            <div class="message success"><?= $conductor_msg ?></div>
        <?php endif; ?>

        <!-- Button to trigger driver modal -->
        <button class="toggle-btn" id="showDriverModal">Add Driver</button>

        <!-- Modal for adding driver -->
        <div id="driverModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeDriverModal">&times;</span>
                <h2>Add Driver</h2>
                <form method="POST">
                    <input type="text" name="driver_name" placeholder="Enter Driver Name" required>
                    <button type="submit" name="add_driver">Add Driver</button>
                </form>
            </div>
        </div>

        <!-- Button to trigger conductor modal -->
        <button class="toggle-btn" id="showConductorModal">Add Conductor</button>

        <!-- Modal for adding conductor -->
        <div id="conductorModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeConductorModal">&times;</span>
                <h2>Add Conductor</h2>
                <form method="POST">
                    <input type="text" name="conductor_name" placeholder="Enter Conductor Name" required>
                    <button type="submit" name="add_conductor">Add Conductor</button>
                </form>
            </div>
        </div>

        <div class="payment-section">
            <h2>Pending Payment Approvals</h2>

            <?php if (mysqli_num_rows($pending_payments) > 0): ?>
                <table class="payment-table">
                    <tr>
                        <th>Passenger</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Boarding Point</th>
                        <th>Dropping Point</th>
                        <th>Bus Name</th>
                        <th>WhatsApp No</th>
                        <th>Amount</th>
                        <th>Screenshot</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($payment = mysqli_fetch_assoc($pending_payments)): ?>
                        <tr>
                            <td><?= htmlspecialchars($payment['passenger_name']) ?></td>
                            <td><?= htmlspecialchars($payment['age']) ?></td>
                            <td><?= htmlspecialchars($payment['gender']) ?></td>
                            <td><?= htmlspecialchars($payment['boarding_point']) ?></td>
                            <td><?= htmlspecialchars($payment['dropping_point']) ?></td>
                            <td><?= htmlspecialchars($payment['bus_name']) ?></td>
                            <td><?= htmlspecialchars($payment['whatsapp_number']) ?></td>
                            <td>₹<?= htmlspecialchars($payment['amount_paid']) ?></td>
                            <td>
                                <?php if (!empty($payment['screenshot_path'])): ?>
                                    <img src="../<?= $payment['screenshot_path'] ?>" class="payment-preview" onclick="window.open('../<?= $payment['screenshot_path'] ?>', '_blank')">
                                <?php else: ?>
                                    No screenshot
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="send_ticket_message.php" method="POST" target="_blank">
                                    <input type="hidden" name="transaction_id" value="<?= $payment['transaction_id'] ?>">
                                    <input type="hidden" name="passenger_name" value="<?= $payment['passenger_name'] ?>">
                                    <input type="hidden" name="age" value="<?= $payment['age'] ?>">
                                    <input type="hidden" name="gender" value="<?= $payment['gender'] ?>">
                                    <input type="hidden" name="boarding_point" value="<?= $payment['boarding_point'] ?>">
                                    <input type="hidden" name="dropping_point" value="<?= $payment['dropping_point'] ?>">
                                    <input type="hidden" name="bus_name" value="<?= $payment['bus_name'] ?>">
                                    <input type="hidden" name="whatsapp_number" value="<?= $payment['whatsapp_number'] ?>">
                                    <input type="hidden" name="amount_paid" value="<?= $payment['amount_paid'] ?>">
                                    <button type="submit" name="payment_action" value="accept" class="accept-btn">Accept</button>
                                    <button type="submit" name="payment_action" value="reject" class="reject-btn">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No pending payments at the moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Modal for driver
        var driverModal = document.getElementById('driverModal');
        var showDriverModal = document.getElementById('showDriverModal');
        var closeDriverModal = document.getElementById('closeDriverModal');
        showDriverModal.onclick = function() {
            driverModal.style.display = "block";
        }
        closeDriverModal.onclick = function() {
            driverModal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target === driverModal) {
                driverModal.style.display = "none";
            }
        }

        // Modal for conductor
        var conductorModal = document.getElementById('conductorModal');
        var showConductorModal = document.getElementById('showConductorModal');
        var closeConductorModal = document.getElementById('closeConductorModal');
        showConductorModal.onclick = function() {
            conductorModal.style.display = "block";
        }
        closeConductorModal.onclick = function() {
            conductorModal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target === conductorModal) {
                conductorModal.style.display = "none";
            }
        }
    </script>
</body>
</html>
