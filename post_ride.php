<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post a Ride - Carpool App</title>
    <style>
        /* Reset & base */
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        /* Header styles */
        .header {
            width: 100%;
            max-width: 1100px;
            background-color: #007bff; /* Bootstrap primary blue */
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            box-sizing: border-box;
            color: white;
            border-radius: 0 0 15px 15px;
            margin: 20px auto 40px auto;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-left .logo {
            height: 50px;
            width: auto;
            object-fit: contain;
            cursor: pointer;
        }

        .header-left h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .header-links a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 12px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
            margin-left: 15px;
        }

        .header-links a:hover {
            background-color: #0056b3;
            color: #e2e6ea;
        }

        /* Container and form */
        .container {
            background-color: white;
            max-width: 600px;
            margin: 0 auto 40px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="datetime-local"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            margin-top: 25px;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
        }

        .back-link a {
            text-decoration: none;
            color: #007bff;
        }

        /* Message container styling */
        .message-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            font-size: 1.1rem;
            color: #333;
        }

        .message-container a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }

        .message-container a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

<?php
session_start();
include 'db.php';

// Check if user is logged in and is driver
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "driver") {
    echo "<div class='message-container'><p>Only drivers can post rides.</p><a href='dashboard.php'>Back to Dashboard</a></div>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $src = trim($_POST["source"]);
    $dest = trim($_POST["destination"]);
    $time = $_POST["ride_date"];
    $seats = (int)$_POST["seats"];
    $fare = (int)$_POST["fare"];

    $user_id = $_SESSION["user_id"];

    // Check vehicle registered
    $vehicle = $conn->query("SELECT vehicle_id FROM vehicles WHERE user_id=$user_id")->fetch_assoc();

    if (!$vehicle) {
        echo "<div class='message-container'><p>You must register a vehicle before posting a ride.</p><a href='add_vehicle.php'>Register Vehicle</a></div>";
        exit;
    }

    $vehicle_id = $vehicle["vehicle_id"];

    $stmt = $conn->prepare("INSERT INTO rides (driver_id, vehicle_id, source, destination, ride_date, seats_available, fare) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssii", $user_id, $vehicle_id, $src, $dest, $time, $seats, $fare);

    if ($stmt->execute()) {
        echo "<div class='message-container'><p>✅ Ride posted successfully!</p><a href='dashboard.php'>Back to Dashboard</a></div>";
        exit;
    } else {
        echo "<div class='message-container'><p>❌ Error posting ride. Please try again later.</p><a href='post_ride.php'>Try Again</a></div>";
        exit;
    }
}
?>

<!-- Header -->
<div class="header">
    <div class="header-left">
        <img src="logo.png" alt="CarPool Logo" class="logo">
        <h1>Post a Ride</h1>
    </div>
    <div class="header-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- Form container -->
<div class="container">
    <h2>Offer a New Ride</h2>
    <form method="post" autocomplete="off" novalidate>
        <label for="source">From:</label>
        <input type="text" id="source" name="source" required>

        <label for="destination">To:</label>
        <input type="text" id="destination" name="destination" required>

        <label for="ride_date">Date & Time:</label>
        <input type="datetime-local" id="ride_date" name="ride_date" required>

        <label for="seats">Seats Available:</label>
        <input type="number" id="seats" name="seats" min="1" required>

        <label for="fare">Fare (₹):</label>
        <input type="number" id="fare" name="fare" min="0" required>

        <button type="submit">Post Ride</button>
    </form>
</div>

</body>
</html>
