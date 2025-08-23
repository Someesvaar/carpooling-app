<?php
session_start();
include 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$user_type = $_SESSION["user_type"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Carpool App</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #f0f2f5;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-left {
            display: flex;
            align-items: center;
        }
        .logo {
            height: 40px;
            width: auto;
            margin-right: 10px;
        }
        .header-left h2 {
            margin: 0;
            font-size: 24px;
        }
        .header-links a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-weight: bold;
        }
        .header-links a:hover {
            text-decoration: underline;
        }
        .container {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .message {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .column-wrapper {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        .column {
            flex: 1;
            min-width: 300px;
            max-width: 400px;
        }
        .ride-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        .ride-card p {
            margin: 8px 0;
            color: #333;
        }
        .ride-card p span {
            font-weight: bold;
            color: #007bff;
        }
        button.cancel-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }
        button.cancel-btn:hover {
            background-color: #a71d2a;
        }
        .no-data {
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>

<!-- Header with logo and nav -->
<div class="header">
    <div class="header-left">
        <img src="logo.png" alt="CarPool Logo" class="logo">
        <h2>CarPool Dashboard</h2>
    </div>
    <div class="header-links">
        <?php if ($user_type === "driver"): ?>
            <a href="post_ride.php">Offer a Ride</a>
            <a href="add_vehicle.php">Add Vehicle</a>
        <?php elseif ($user_type === "passenger"): ?>
            <a href="book_ride.php">Book a Ride</a>
            <a href="grievance.php">Submit Grievance</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- Main content -->
<div class="container">

<?php
// Show messages if any
if (isset($_SESSION['success'])) {
    echo "<div class='message success'>{$_SESSION['success']}</div>";
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo "<div class='message error'>{$_SESSION['error']}</div>";
    unset($_SESSION['error']);
}
?>

<?php if ($user_type === "passenger"): ?>
    <div class="column-wrapper">

        <!-- Available Rides Column -->
        <div class="column">
            <h2>Available Rides</h2>
            <?php
            $available_rides = $conn->query("SELECT r.ride_id, u.name, r.source, r.destination, r.ride_date, r.seats_available, r.fare 
                                            FROM rides r JOIN users u ON r.driver_id = u.user_id 
                                            WHERE r.seats_available > 0 AND r.ride_date > NOW() 
                                            ORDER BY r.ride_date ASC");

            if ($available_rides->num_rows > 0) {
                while ($row = $available_rides->fetch_assoc()) {
                    echo "<div class='ride-card'>
                        <p><span>Driver:</span> ".htmlspecialchars($row['name'])."</p>
                        <p><span>From:</span> ".htmlspecialchars($row['source'])."</p>
                        <p><span>To:</span> ".htmlspecialchars($row['destination'])."</p>
                        <p><span>Date:</span> ".htmlspecialchars($row['ride_date'])."</p>
                        <p><span>Seats Available:</span> ".htmlspecialchars($row['seats_available'])."</p>
                        <p><span>Fare:</span> ₹".htmlspecialchars($row['fare'])."</p>
                    </div>";
                }
            } else {
                echo "<p class='no-data'>No available rides at the moment.</p>";
            }
            ?>
        </div>

        <!-- Booked Rides Column -->
        <div class="column">
            <h2>Your Booked Rides</h2>
            <?php
            // Query confirmed bookings by this passenger with their ride info
            $booked_rides = $conn->query("
                SELECT 
                    b.booking_id,
                    r.ride_id,
                    u.name AS driver_name,
                    r.source,
                    r.destination,
                    r.ride_date,
                    b.status
                FROM bookings b
                JOIN rides r ON b.ride_id = r.ride_id
                JOIN users u ON r.driver_id = u.user_id
                WHERE b.passenger_id = $user_id
                ORDER BY r.ride_date DESC
            ");

            if ($booked_rides->num_rows > 0) {
                while ($row = $booked_rides->fetch_assoc()) {
                    echo "<div class='ride-card'>
                        <p><span>Driver:</span> ".htmlspecialchars($row['driver_name'])."</p>
                        <p><span>From:</span> ".htmlspecialchars($row['source'])."</p>
                        <p><span>To:</span> ".htmlspecialchars($row['destination'])."</p>
                        <p><span>Date:</span> ".htmlspecialchars($row['ride_date'])."</p>
                        <p><span>Status:</span> ".htmlspecialchars($row['status'])."</p>";

                    // Show cancel button only if booking is confirmed
                    if ($row['status'] === 'confirmed') {
                        echo "<form method='GET' action='cancel_booking.php' onsubmit='return confirm(\"Are you sure you want to cancel this booking?\");'>
                                <input type='hidden' name='ride_id' value='" . intval($row['ride_id']) . "'>
                                <button type='submit' class='cancel-btn'>Cancel Booking</button>
                              </form>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p class='no-data'>You have not booked any rides yet.</p>";
            }
            ?>
        </div>

        <!-- Rides as Driver (optional for passengers if any) -->
        <div class="column">
            <h2>Your Rides</h2>
            <?php
            $your_rides = $conn->query("SELECT source, destination, ride_date, seats_available, fare 
                                        FROM rides 
                                        WHERE driver_id = $user_id
                                        ORDER BY ride_date DESC");

            if ($your_rides->num_rows > 0) {
                while ($row = $your_rides->fetch_assoc()) {
                    echo "<div class='ride-card'>
                        <p><span>From:</span> ".htmlspecialchars($row['source'])."</p>
                        <p><span>To:</span> ".htmlspecialchars($row['destination'])."</p>
                        <p><span>Date:</span> ".htmlspecialchars($row['ride_date'])."</p>
                        <p><span>Seats Available:</span> ".htmlspecialchars($row['seats_available'])."</p>
                        <p><span>Fare:</span> ₹".htmlspecialchars($row['fare'])."</p>
                    </div>";
                }
            } else {
                echo "<p class='no-data'>You have not posted any rides yet.</p>";
            }
            ?>
        </div>
    </div>

<?php elseif ($user_type === "driver"): ?>
    <h2>Your Posted Rides</h2>
    <?php
    $driver_rides = $conn->query("SELECT source, destination, ride_date, seats_available, fare 
                                   FROM rides WHERE driver_id = $user_id ORDER BY ride_date DESC");

    if ($driver_rides->num_rows > 0) {
        while ($row = $driver_rides->fetch_assoc()) {
            echo "<div class='ride-card'>
                <p><span>From:</span> ".htmlspecialchars($row['source'])."</p>
                <p><span>To:</span> ".htmlspecialchars($row['destination'])."</p>
                <p><span>Date:</span> ".htmlspecialchars($row['ride_date'])."</p>
                <p><span>Seats Available:</span> ".htmlspecialchars($row['seats_available'])."</p>
                <p><span>Fare:</span> ₹".htmlspecialchars($row['fare'])."</p>
            </div>";
        }
    } else {
        echo "<p>No rides posted yet.</p>";
    }
    ?>
<?php endif; ?>

<?php include 'footer.php'; ?>

</div>

</body>
</html>
