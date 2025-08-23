<?php
session_start();
include 'db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "passenger") {
    die("Unauthorized access.");
}

$passenger_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    list($ride_id, $driver_id) = explode('-', $_POST["ride_id"]);
    $message = $_POST["message"];

    $stmt = $conn->prepare("INSERT INTO grievances (passenger_id, driver_id, ride_id, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $passenger_id, $driver_id, $ride_id, $message);
    $stmt->execute();

    echo "<p style='text-align:center; font-family:sans-serif; color:green;'>Grievance submitted successfully! <a href='dashboard.php'>Back to Dashboard</a></p>";
    exit;
}

// Fetch booked rides with driver info
$result = $conn->query("SELECT b.ride_id, u.name as driver_name, u.user_id as driver_id
                        FROM bookings b
                        JOIN rides r ON b.ride_id = r.ride_id
                        JOIN users u ON r.driver_id = u.user_id
                        WHERE b.passenger_id = $passenger_id");

$ride_options = [];
while ($row = $result->fetch_assoc()) {
    $ride_id = $row['ride_id'];
    if (!isset($ride_options[$ride_id])) {
        $ride_options[$ride_id] = $row; // only add once
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Grievance</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 500px;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 25px;
        }

        label, select, textarea {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            font-size: 15px;
        }

        select, textarea {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Report a Grievance</h2>
    <form method="post">
        <label for="ride_id">Select Ride:</label>
        <select name="ride_id" required>
            <option disabled selected value="">-- Choose a Ride --</option>
            <?php foreach ($ride_options as $opt): ?>
                <option value="<?= $opt['ride_id'] ?>-<?= $opt['driver_id'] ?>">
                    <?= htmlspecialchars($opt['driver_name']) ?> - Ride ID: <?= $opt['ride_id'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="message">Describe the issue:</label>
        <textarea name="message" rows="5" placeholder="Write your grievance here..." required></textarea>

        <button type="submit">Submit Grievance</button>
    </form>
</div>

</body>
</html>
