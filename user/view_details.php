<?php
include('../includes/db_connect.php');  

// Handle Deletion (if a delete link is clicked)
if (isset($_GET['delete_id']) && isset($_GET['table']) && isset($_GET['id_column'])) {    
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);    
    $table = mysqli_real_escape_string($conn, $_GET['table']);    
    $id_column = mysqli_real_escape_string($conn, $_GET['id_column']);    

    $allowed_tables = ['schedule_details'];    

    if (in_array($table, $allowed_tables)) {        
        $deleteQuery = "DELETE FROM $table WHERE $id_column = '$delete_id'";
        if (mysqli_query($conn, $deleteQuery)) {            
            echo "<script>alert('Record deleted successfully!'); window.location.href='view_details.php';</script>";        
        } else {            
            echo "<script>alert('Error deleting record!');</script>";        
        }    
    }
}

// Get filter values from GET parameters (from the front page)
$origin       = isset($_GET['origin']) ? mysqli_real_escape_string($conn, $_GET['origin']) : "";
$destination  = isset($_GET['destination']) ? mysqli_real_escape_string($conn, $_GET['destination']) : "";
$journey_date = isset($_GET['journey_date']) ? mysqli_real_escape_string($conn, $_GET['journey_date']) : "";

// Build the filter clause using the entered criteria
$filterClause = "WHERE 1";  
if (!empty($origin)) {
    $filterClause .= " AND r.start_point = '$origin'";
}
if (!empty($destination)) {
    $filterClause .= " AND r.end_point = '$destination'";
}
if (!empty($journey_date)) {
    $filterClause .= " AND s.journey_date = '$journey_date'";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Schedule + Route Details</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* General Styling */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background: url('../images/search_result.jpeg') no-repeat center center fixed; /* Add your background image URL */
            background-size: cover; /* Ensures the image covers the entire page */
            overflow-x: hidden;
        }

        /* Content Styling */
        .content {
            position: relative;
            z-index: 1;
            padding: 20px;
            text-align: center;
        }

        h2 {
            color: white;
            font-size: 36px;
            margin-top: 40px;
            font-weight: bold;
        }

        /* Gradient Overlay Styling */
        .gradient-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4); /* Darker overlay */
            z-index: -1;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            opacity: 0;
            animation: slideIn 1s forwards;
        }

        /* Animation for table rows */
        @keyframes slideIn {
            0% {
                transform: translateY(50px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        /* Button Styling */
        .view-seat-btn {
            padding: 8px 12px;
            border-radius: 4px;
            background-color: #2ecc71;
            color: white;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s ease;
        }

        .view-seat-btn:hover {
            background-color: #27ae60;
            transform: scale(1.1);
        }

        /* Search Criteria Styling */
        .search-criteria {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            color: #fff;
        }

        /* Responsive Table */
        @media (max-width: 768px) {
            table {
                display: block;
                width: 100%;
                overflow-x: auto;
                white-space: nowrap;
            }

            th, td {
                padding: 10px;
                font-size: 14px;
            }

            .view-seat-btn {
                padding: 6px 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="gradient-overlay"></div> <!-- Gradient Overlay for better contrast -->

<div class="content">
    <?php
    // Display the search criteria entered by the user
    if (!empty($origin) || !empty($destination) || !empty($journey_date)) {
        echo "<div class='search-criteria'>";
        echo "Search Criteria: ";
        echo "From: <strong>" . htmlspecialchars($origin) . "</strong>, ";
        echo "To: <strong>" . htmlspecialchars($destination) . "</strong>, ";
        echo "Journey Date: <strong>" . htmlspecialchars($journey_date) . "</strong>";
        echo "</div>";
    }
    ?>

    <h2>Buses that match your plans...</h2>

    <?php
    $query = "SELECT
                s.schedule_id,
                s.bus_id,
                b.bus_name,
                b.bus_type,
                b.price_per_seat,
                s.route_name,
                s.journey_date,
                s.departure_time,
                s.arrival_time,
                r.start_point,
                r.end_point,
                r.distance_km,
                r.estimated_time
              FROM schedule_details s
              LEFT JOIN route_details r ON s.route_name = r.route_name
              LEFT JOIN bus_details b ON s.bus_id = b.bus_id
              $filterClause";

    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table>
                <tr>
                    <th>Schedule ID</th>
                    <th>Bus ID</th>
                    <th>Bus Name</th>
                    <th>Bus Type</th>
                    <th>Price/Seat</th>
                    <th>Route Name</th>
                    <th>Journey Date</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Start Point</th>
                    <th>End Point</th>
                    <th>Distance (KM)</th>
                    <th>Estimated Time</th>
                    <th>Action</th>
                </tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['schedule_id']) . "</td>
                    <td>" . htmlspecialchars($row['bus_id']) . "</td>
                    <td>" . htmlspecialchars($row['bus_name']) . "</td>
                    <td>" . htmlspecialchars($row['bus_type']) . "</td>
                    <td>" . htmlspecialchars($row['price_per_seat']) . "</td>
                    <td>" . htmlspecialchars($row['route_name']) . "</td>
                    <td>" . htmlspecialchars($row['journey_date']) . "</td>
                    <td>" . htmlspecialchars($row['departure_time']) . "</td>
                    <td>" . htmlspecialchars($row['arrival_time']) . "</td>
                    <td>" . htmlspecialchars($row['start_point']) . "</td>
                    <td>" . htmlspecialchars($row['end_point']) . "</td>
                    <td>" . htmlspecialchars($row['distance_km']) . "</td>
                    <td>" . htmlspecialchars($row['estimated_time']) . "</td>
                    <td>
                        <a href='../user/select_seat.php?bus_id=" . $row['bus_id'] . "&schedule_id=" . $row['schedule_id'] . "&journey_date=" . $row['journey_date'] . "' class='view-seat-btn'>View Seats</a>
                    </td>
                  </tr>";
        }
        echo "</table><br>";
    } else {
        echo "<p style='text-align: center; color: #fff;'>No schedule/route/bus records found for the specified criteria.</p>";
    }
    ?>

</div>

</body>
</html>
