<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'passenger') {
    // Only logged-in passengers can cancel bookings
    header("Location: login.php");
    exit;
}

if (!isset($_GET['ride_id'])) {
    // ride_id is required
    header("Location: dashboard.php");
    exit;
}

$ride_id = intval($_GET['ride_id']);
$passenger_id = $_SESSION['user_id'];

// Check if the booking exists and is confirmed
$checkBooking = $conn->query("SELECT * FROM bookings WHERE ride_id = $ride_id AND passenger_id = $passenger_id AND status = 'confirmed'");

if ($checkBooking->num_rows === 0) {
    // No such confirmed booking found
    $_SESSION['error'] = "No confirmed booking found to cancel.";
    header("Location: dashboard.php");
    exit;
}

// Start transaction to ensure consistency
$conn->begin_transaction();

try {
    // Update booking status to 'cancelled'
    $updateBooking = $conn->query("UPDATE bookings SET status = 'cancelled' WHERE ride_id = $ride_id AND passenger_id = $passenger_id AND status = 'confirmed'");
    
    if (!$updateBooking) {
        throw new Exception("Failed to update booking status.");
    }

    // Increment seats_available in rides table
    $updateSeats = $conn->query("UPDATE rides SET seats_available = seats_available WHERE ride_id = $ride_id");
    if (!$updateSeats) {
        throw new Exception("Failed to update available seats.");
    }

    // Commit transaction
    $conn->commit();

    $_SESSION['success'] = "Booking cancelled successfully.";
    header("Location: dashboard.php");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Error cancelling booking: " . $e->getMessage();
    header("Location: dashboard.php");
    exit;
}
?>
