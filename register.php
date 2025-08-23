<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Carpool App</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: url('https://images.unsplash.com/photo-1507433360992-f6c0b9f1d059') no-repeat center center;
            background-size: cover;
            height: 100vh;
        }

        .header {
            background-color: #007bff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
        }

        .header .logo {
            display: flex;
            align-items: center;
        }

        .header .logo img {
            height: 40px;
            margin-right: 10px;
        }

        .header h1 {
            font-size: 22px;
            margin: 0;
        }

        .header a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 400px;
            margin: 40px auto;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        input[type="text"], input[type="email"], input[type="password"], select {
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

<div class="header">
    <div class="logo">
        <img src="logo.png" alt="Carpool Logo">
        <h1>CarPool</h1>
    </div>
    <a href="home.php">Home</a>
</div>

<?php
include 'db.php';
$email_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $user_type = $_POST["user_type"];

    $check_stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $email_error = "This email is already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, user_type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $password, $user_type);
        $stmt->execute();
        echo "<script>alert('Registered successfully! Redirecting to login.'); window.location.href='login.php';</script>";
        exit;
    }
}
?>

<div class="form-container">
    <?php
$user_type = isset($_GET['user_type']) ? $_GET['user_type'] : null;
?>
    <h2>User Registration</h2>
    <form method="post">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
        <?php if (!empty($email_error)): ?>
            <div style="color: red; font-size: 14px; margin-top: 4px;"><?= $email_error ?></div>
        <?php endif; ?>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <input type="password" name="password" placeholder="Password" required>
            <select name="user_type" required>
            <?php if (is_null($user_type)): ?>
                <option value="" disabled selected>Select Type</option>
                <option value="driver">Driver</option>
                <option value="passenger">Passenger</option>
            <?php elseif ($user_type == "driver"): ?>
                <option value="driver" selected>Driver</option>
            <?php elseif ($user_type == "passenger"): ?>
                <option value="passenger" selected>Passenger</option>
    <?php endif; ?>
</select>

        <button type="submit">Register</button>
    </form>
    <a class="back-link" href="login.php">Already have an account? Login</a>
</div>

</body>
</html>
s