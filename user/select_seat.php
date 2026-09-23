
<?php
include('../includes/db_connect.php');

// Handle AJAX seat update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $bus_id = mysqli_real_escape_string($conn, $_POST['bus_id']);
    $seat_no = mysqli_real_escape_string($conn, $_POST['seat_no']);

    if ($_POST['action'] === 'book') {
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $insertQuery = "INSERT INTO user_booking_details (bus_id, seat_no, gender)
                        VALUES ('$bus_id', '$seat_no', '$gender')
                        ON DUPLICATE KEY UPDATE gender='$gender'";
        echo mysqli_query($conn, $insertQuery) ? "success" : "error";
        exit;
    }

    if ($_POST['action'] === 'cancel') {
        $deleteQuery = "DELETE FROM user_booking_details WHERE bus_id='$bus_id' AND seat_no='$seat_no'";
        echo mysqli_query($conn, $deleteQuery) ? "canceled" : "error";
        exit;
    }
}

if (!isset($_GET['bus_id'])) {
    echo "No bus selected.";
    exit;
}

$bus_id = mysqli_real_escape_string($conn, $_GET['bus_id']);

// Fetch bus_name based on bus_id
$busNameQuery = "SELECT bus_name FROM bus_details WHERE bus_id = $bus_id";
$busNameResult = mysqli_query($conn, $busNameQuery);
$busName = (mysqli_num_rows($busNameResult) > 0) ? mysqli_fetch_assoc($busNameResult)['bus_name'] : 'Unknown Bus';

// Fetch seat layout details
$query = "SELECT total_rows, total_columns FROM seat_layout WHERE bus_id = $bus_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "No layout found for this bus.";
    exit;
}

$layout = mysqli_fetch_assoc($result);
$rows = $layout['total_rows'];
$cols = $layout['total_columns'];

$busQuery = mysqli_query($conn, "SELECT price_per_seat FROM bus_details WHERE bus_id = $bus_id");
$busInfo = mysqli_fetch_assoc($busQuery);
$pricePerSeat = $busInfo['price_per_seat'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Select Seat</title>
    <style>
        body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: url('../images/Photo by A G on Unsplash.jpeg') no-repeat center center fixed;
    background-size: cover;
    color: #fff;
}


        @keyframes bgShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        table {
            margin: 20px auto;
            background-color: rgba(255, 255, 255, 0.95);
            border-collapse: collapse;
            color: #333;
            border-radius: 8px;
        }

        td {
            width: 60px; height: 60px; text-align: center; cursor: pointer;
            border-radius: 5px; font-weight: bold;
            transition: transform 0.3s ease;
        }

        td.available:hover {
            animation: jump 0.5s ease;
        }

        @keyframes jump {
            0%   { transform: translateY(0); }
            50%  { transform: translateY(-8px); }
            100% { transform: translateY(0); }
        }

        .available { background-color: #4CAF50; color: white; }
        .booked-male { background-color: #F44336; color: white; cursor: not-allowed; }
        .booked-female { background-color: #2196F3; color: white; cursor: not-allowed; }
        .selected { background-color: #ff9800; color: white; }

        .legend {
            text-align: center;
            margin-top: 20px;
        }

        .legend-item {
            display: inline-block; padding: 8px 15px; margin-right: 10px;
            border-radius: 5px; color: white; font-weight: bold;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
        }

        #genderModal, #overlay {
            display: none; position: fixed; z-index: 1000;
        }

        #genderModal {
            top: 50%; left: 50%; transform: translate(-50%, -50%);
            background: white; padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            color: #000;
        }

        #overlay {
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 999;
        }

        .gender-btn {
            padding: 10px 20px; margin: 10px;
            border: none; border-radius: 5px;
            color: white; font-weight: bold; cursor: pointer;
        }

        #maleBtn { background-color: #F44336; }
        #femaleBtn { background-color: #2196F3; }
        #cancelBtn { background-color: #9E9E9E; }
        #cancelSeatBtn { background-color: #ff9800; }
    </style>
</head>
<body>

       

<h2>Select Seat for Busname: <?= htmlspecialchars($busName) ?> (Bus ID: <?= $bus_id ?>)</h2>

<div class="legend">
    <span class="legend-item" style="background-color: #4CAF50;">Available</span>
    <span class="legend-item" style="background-color: #F44336;">Booked (Male)</span>
    <span class="legend-item" style="background-color: #2196F3;">Booked (Female)</span>
</div>

<table border="1" cellpadding="5">
<?php for ($i = 1; $i <= $rows; $i++): ?>
    <tr>
        <?php for ($j = 1; $j <= $cols; $j++):
            $seat_no = "R{$i}C{$j}";
            $seat_no_escaped = mysqli_real_escape_string($conn, $seat_no);
            $checkSeat = mysqli_query($conn, "SELECT gender FROM user_booking_details WHERE bus_id = $bus_id AND seat_no = '$seat_no_escaped'");
            if (mysqli_num_rows($checkSeat) > 0) {
                $gender = strtolower(mysqli_fetch_assoc($checkSeat)['gender']);
                $class = ($gender === 'female') ? 'booked-female' : 'booked-male';
            } else {
                $class = 'available';
            }
        ?>
            <td class="<?= $class ?>" data-seat="<?= $seat_no ?>" onclick="handleSeatClick(this)">
                <?= $seat_no ?>
            </td>
        <?php endfor; ?>
    </tr>
<?php endfor; ?>
</table>

<!-- Gender Modal -->
<div id="overlay"></div>
<div id="genderModal">
    <h3>Seat: <span id="modalSeatNumber"></span></h3>
    <form id="genderForm" method="GET" action="passenger_details.php">
        <input type="hidden" name="bus_id" value="<?= $bus_id ?>">
        <input type="hidden" name="seat_no" id="seatInput">
        <input type="hidden" name="gender" id="genderInput">
        <input type="hidden" name="total_price" value="<?= $pricePerSeat ?>">
        <div id="bookingButtons">
            <button type="button" class="gender-btn" id="maleBtn" onclick="submitGender('Male')">Male</button>
            <button type="button" class="gender-btn" id="femaleBtn" onclick="submitGender('Female')">Female</button>
            <button type="button" class="gender-btn" id="cancelBtn" onclick="closeModal()">Close</button>
        </div>
        <div id="cancelBookingBtn" style="display: none;">
            <button type="button" class="gender-btn" id="cancelSeatBtn" onclick="cancelSeat()">Cancel Seat</button>
        </div>
    </form>
</div>

<script>
    let selectedSeat = null;

    function handleSeatClick(td) {
        selectedSeat = td;
        const seatNo = td.getAttribute('data-seat');
        document.getElementById('modalSeatNumber').textContent = seatNo;
        document.getElementById('seatInput').value = seatNo;
        document.getElementById('overlay').style.display = 'block';
        document.getElementById('genderModal').style.display = 'block';

        // If already booked by this session, allow cancel
        if (td.classList.contains('booked-male') || td.classList.contains('booked-female')) {
            document.getElementById('bookingButtons').style.display = 'none';
            document.getElementById('cancelBookingBtn').style.display = 'block';
        } else {
            document.getElementById('bookingButtons').style.display = 'block';
            document.getElementById('cancelBookingBtn').style.display = 'none';
        }
    }

    function submitGender(gender) {
        const seatNo = selectedSeat.getAttribute('data-seat');
        const busId = <?= $bus_id ?>;

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "select_seat.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status === 200 && xhr.responseText.trim() === "success") {
                if (gender === 'Male') {
                    selectedSeat.classList.remove('available');
                    selectedSeat.classList.add('booked-male');
                } else if (gender === 'Female') {
                    selectedSeat.classList.remove('available');
                    selectedSeat.classList.add('booked-female');
                }
                document.getElementById('genderInput').value = gender;
                document.getElementById('genderForm').submit();
            } else {
                alert("Failed to book seat.");
            }
        };
        xhr.send("bus_id=" + busId + "&seat_no=" + seatNo + "&gender=" + gender + "&action=book");
    }

    function cancelSeat() {
        const seatNo = selectedSeat.getAttribute('data-seat');
        const busId = <?= $bus_id ?>;

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "select_seat.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status === 200 && xhr.responseText.trim() === "canceled") {
                selectedSeat.classList.remove('booked-male', 'booked-female');
                selectedSeat.classList.add('available');
                closeModal();
            } else {
                alert("Failed to cancel seat.");
            }
        };
        xhr.send("bus_id=" + busId + "&seat_no=" + seatNo + "&action=cancel");
    }

    function closeModal() {
        document.getElementById('overlay').style.display = 'none';
        document.getElementById('genderModal').style.display = 'none';
    }
</script>

</body>
</html>