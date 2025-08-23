<?php
$fare = 250; // Replace with dynamic fare from database
$ride_id = 101; // Replace with ride ID
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pay Now</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>

<h2>Fare Amount: ₹<?php echo $fare; ?></h2>
<button id="rzp-button">Pay ₹<?php echo $fare; ?></button>

<script>
var options = {
    "key": "rzp_test_Pp6XhNREzIgBuu", // Replace with your key ID
    "amount": "<?php echo $fare * 100; ?>", // in paise
    "currency": "INR",
    "name": "Carpool App",
    "description": "Ride Payment",
    "handler": function (response){
        // Redirect to success page
        window.location.href = "payment_success.php?ride_id=<?php echo $ride_id; ?>&payment_id=" + response.razorpay_payment_id;
    },
    "prefill": {
        "name": "",
        "email": ""
    },
    "theme": {
        "color": "#3399cc"
    }
};
var rzp = new Razorpay(options);
document.getElementById('rzp-button').onclick = function(e){
    rzp.open();
    e.preventDefault();
}
</script>

</body>
</html>
