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
        /* Your existing styles here (unchanged)... */
        /* ... */
        
        /* Modal Styles for Confirmation Popup */
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
        .modal-confirm {
            display: none; 
            position: fixed; 
            z-index: 10000; 
            left: 0; top: 0;
            width: 100%; height: 100%; 
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-confirm.active {
            display: flex;
        }
        .modal-confirm-content {
            background: white;
            border-radius: 10px;
            padding: 25px 30px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            text-align: center;
        }
        .modal-confirm-content h3 {
            margin: 0 0 15px 0;
            color: #dc3545;
        }
        .modal-confirm-content p {
            margin-bottom: 25px;
            color: #333;
            font-size: 16px;
        }
        .modal-confirm-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .btn-confirm {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .btn-confirm:hover {
            background-color: #a71d2a;
        }
        .btn-cancel {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .btn-cancel:hover {
            background-color: #565e64;
        }
    </style>
</head>
<body>

<!-- Header + container as you already have -->
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
                    $ride_id = intval($row['ride_id']);
                    $driver_name = htmlspecialchars($row['driver_name']);
                    $source = htmlspecialchars($row['source']);
                    $destination = htmlspecialchars($row['destination']);
                    $ride_date = htmlspecialchars($row['ride_date']);
                    $status = htmlspecialchars($row['status']);

                    echo "<div class='ride-card'>
                        <p><span>Driver:</span> $driver_name</p>
                        <p><span>From:</span> $source</p>
                        <p><span>To:</span> $destination</p>
                        <p><span>Date:</span> $ride_date</p>
                        <p><span>Status:</span> $status</p>";

                    if ($status === 'confirmed') {
                        // Use data attributes to pass info to JS
                        echo "<form method='GET' action='cancel_booking.php' class='cancel-form'>
                                <input type='hidden' name='ride_id' value='$ride_id'>
                                <button type='button' class='cancel-btn' 
                                    data-ride-id='$ride_id' 
                                    data-driver='$driver_name' 
                                    data-source='$source' 
                                    data-destination='$destination' 
                                    data-date='$ride_date'>
                                    Cancel Booking
                                </button>
                              </form>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p class='no-data'>You have not booked any rides yet.</p>";
            }
            ?>
        </div>

        <!-- Your Rides Column -->
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

<!-- Confirmation Modal -->
<div class="modal-confirm" id="cancelModal">
    <div class="modal-confirm-content">
        <h3>Confirm Cancellation</h3>
        <p id="modalText">Are you sure you want to cancel this booking?</p>
        <div class="modal-confirm-buttons">
            <button class="btn-confirm" id="confirmCancelBtn">Yes, Cancel</button>
            <button class="btn-cancel" id="cancelCancelBtn">No, Keep Booking</button>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

</div>

<script>
    const cancelButtons = document.querySelectorAll('.cancel-btn');
    const modal = document.getElementById('cancelModal');
    const modalText = document.getElementById('modalText');
    const confirmBtn = document.getElementById('confirmCancelBtn');
    const cancelBtn = document.getElementById('cancelCancelBtn');

    let currentForm = null;

    cancelButtons.forEach(button => {
        button.addEventListener('click', () => {
            const driver = button.dataset.driver;
            const source = button.dataset.source;
            const destination = button.dataset.destination;
            const date = button.dataset.date;

            modalText.textContent = `Are you sure you want to cancel your booking with driver ${driver} from ${source} to ${destination} on ${date}?`;
            modal.classList.add('active');

            // Store the form to submit if confirmed
            currentForm = button.closest('form');
        });
    });

    confirmBtn.addEventListener('click', () => {
        if (currentForm) {
            currentForm.submit();
        }
    });

    cancelBtn.addEventListener('click', () => {
        modal.classList.remove('active');
        currentForm = null;
    });

    // Close modal on outside click
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
            currentForm = null;
        }
    });
</script>

</body>
</html>
