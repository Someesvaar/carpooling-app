<?php
// Start the session and include DB connection
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/carpool/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT user_id, password, user_type FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $hashed, $type);

    if ($stmt->fetch() && password_verify($password, $hashed)) {
        $_SESSION["user_id"] = $id;
        $_SESSION["user_type"] = $type;
        header("Location: /carpool/dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Carpool App</title>
    <link rel="stylesheet" href="/carpool/style.css">
    <style>
        /* General body and background */
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: url('https://drawdown.org/sites/default/files/solutions2020/solution_carpooling.jpg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
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

        /* Login form container */
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 400px;
            margin: 50px auto;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        input[type="email"], input[type="password"] {
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
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        /* Ensure footer or other elements don't overlap */
        footer {
            margin-top: auto;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="logo">
        <img src="/carpool/logo.png" alt="CarPool Logo">
        <h1>CarPool</h1>
    </div>
    <div>
        <a href="/carpool/home.php">Home</a>
    </div>
</div>

<!-- Login Form -->
<div class="form-container">
    <h2>Login</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <a class="back-link" href="/carpool/register.php">Don't have an account? Register</a>
</div>

</body>
</html>
