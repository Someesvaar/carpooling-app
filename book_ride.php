<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book a Ride - Carpool App</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .navbar {
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }

        h1 {
            font-size: 24px;
            margin: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 40px;
            height: 40px;
            margin-right: 10px;
            border-radius: 50%;
            object-fit: cover;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
        }

        .header-links a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
            transition: color 0.2s ease;
        }

        .header-links a:hover {
            color: #d9e6f2;
        }

        .ride-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .ride-info {
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
        }

        button {
            margin-top: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .confirmation {
            text-align: center;
            padding: 20px;
            color: green;
            font-size: 18px;
        }

        .container {
            padding: 20px;
        }
    </style>
</head>
<body>

<?php
session_start();
$user_type = $_SESSION["user_type"] ?? "";
?>

<!-- Header with logo and links -->
<div class="header">
    <div class="header-left">
        <img src="logo.png" alt="CarPool Logo" class="logo">
        <h2>CarPool</h2>
    </div>
    <div class="header-links">
        <a href="home.php">Home</a>
        <?php if ($user_type === "driver"): ?>
            <a href="post_ride.php">Offer Ride</a>
        <?php elseif ($user_type === "passenger"): ?>
            <a href="book_ride.php">Book Ride</a>
            <a href="grievance.php">Grievance</a>
        <?php endif; ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- Sub navbar -->
<div class="navbar">
    <h1>Book a Ride</h1>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
<?php
include 'db.php';

if ($_SESSION["user_type"] !== "passenger") {
    die("<div class='ride-info'>Only passengers can book rides.</div>");
}

// Fetch rides
$result = $conn->query("SELECT r.ride_id, u.name, r.source, r.destination, r.ride_date, r.fare, r.seats_available
                        FROM rides r 
                        JOIN users u ON r.driver_id = u.user_id
                        WHERE r.seats_available > 0 AND r.ride_date > NOW()");

echo "<h2>Available Rides</h2>";

while ($row = $result->fetch_assoc()) {
    echo "<div class='ride-card'>
            <form method='post' action='payment_start.php'>
                <input type='hidden' name='ride_id' value='{$row['ride_id']}'>
                <input type='hidden' name='fare' value='{$row['fare']}'>
                <div class='ride-info'>
                    <strong>Driver:</strong> {$row['name']}<br>
                    <strong>From:</strong> {$row['source']}<br>
                    <strong>To:</strong> {$row['destination']}<br>
                    <strong>Date & Time:</strong> {$row['ride_date']}<br>
                    <strong>Fare:</strong> ₹{$row['fare']}<br>
                    <strong>Seats Available:</strong> {$row['seats_available']}
                </div>
                <button type='submit'>Pay & Book</button>
            </form>
        </div>";
}
?>
</div>
</body>
</html>
