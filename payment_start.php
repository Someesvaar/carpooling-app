<?php
session_start();

// Get Razorpay key
$key_id = "rzp_test_Pp6XhNREzIgBuu";  // Replace with your real key_id

if (!isset($_POST['ride_id'], $_POST['fare'])) {
    die("Invalid request.");
}

$ride_id = $_POST['ride_id'];
$fare = $_POST['fare'];
$passenger_id = $_SESSION['user_id'] ?? null;

if (!$passenger_id) {
    die("Unauthorized access.");
}

// Convert fare to paisa for Razorpay (e.g. ₹100 -> 10000)
$amount = $fare * 100;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Processing Payment...</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
<script>
var options = {
    "key": "<?php echo $key_id; ?>",
    "amount": "<?php echo $amount; ?>", // in paisa
    "currency": "INR",
    "name": "CarPool",
    "description": "Ride Booking Payment",
    "image": "logo.png",
    "handler": function (response){
        // ✅ Redirect with payment_id, ride_id, and passenger_id
        window.location.href = "payment_success.php?payment_id=" + response.razorpay_payment_id +
                               "&ride_id=<?php echo $ride_id; ?>" +
                               "&passenger_id=<?php echo $passenger_id; ?>";
    },
    "prefill": {
        "name": "Passenger <?php echo $passenger_id; ?>",
        "email": "test@example.com"
    },
    "theme": {
        "color": "#007bff"
    }
};
var rzp1 = new Razorpay(options);
rzp1.open();
</script>
</body>
</html>
