<?php
session_start();
include 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_number = $_POST["vehicle_number"];
    $model = $_POST["model"];
    $capacity = $_POST["seating_capacity"];
    $user_id = $_SESSION["user_id"];

    $stmt = $conn->prepare("INSERT INTO vehicles (user_id, vehicle_number, model, seating_capacity) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $user_id, $vehicle_number, $model, $capacity);
    $stmt->execute();

    echo "<script>alert('Vehicle added successfully!'); window.location.href='dashboard.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Vehicle - Carpool App</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: url('https://images.unsplash.com/photo-1507433360992-f6c0b9f1d059') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-link {
            display: block;
            margin-top: 15px;
            text-align: center;
            color: #007bff;
        }
    </style>
</head>
<body>



<div class="form-container">
    <h2>Vehicle Details</h2>
    <form method="post">
        <input type="text" name="vehicle_number" placeholder="Vehicle Number" required>
        <input type="text" name="model" placeholder="Car Model" required>
        <input type="number" name="seating_capacity" placeholder="Seating Capacity" required>
        <button type="submit">Add Vehicle</button>
    </form>
    <a class="back-link" href="dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>
