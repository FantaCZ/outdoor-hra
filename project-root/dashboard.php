<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>Vítejte na dashboardu, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <a href="logout.php">Odhlásit se</a>
</body>
</html>