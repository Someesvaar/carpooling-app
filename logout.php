<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Carpool App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
session_start();
session_destroy();
header("Location: dashboard.php");
?>
</body>
</html>