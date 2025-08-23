<?php
session_start();
include 'db.php';

$ride_id = $_GET['ride_id'] ?? null;
$passenger_id = $_GET['passenger_id'] ?? null;
$payment_id = $_GET['payment_id'] ?? null;

if (!$ride_id || !$passenger_id || !$payment_id) {
    die("Missing ride ID, passenger ID, or payment ID.");
}

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO bookings (ride_id, passenger_id, status) VALUES (?, ?, 'confirmed')");
$stmt->bind_param("ii", $ride_id, $passenger_id);
$stmt->execute();

// Reduce seat count
$conn->query("UPDATE rides SET seats_available = seats_available - 1 WHERE ride_id = $ride_id");

$_SESSION['success'] = "✅ Ride booked successfully! Payment ID: $payment_id";
header("Location: dashboard.php");
exit;
?>
