<?php
// Zahrnutí souboru pro připojení k databázi
include 'db.php';

// Zpracování formuláře
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Ověření, zda hesla odpovídají
    if ($password !== $confirm_password) {
        echo "Hesla se neshodují!";
        exit;
    }

    // Hashování hesla pro bezpečnost
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Kontrola, zda uživatelské jméno již existuje
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo "Toto uživatelské jméno již existuje!";
        exit;
    }

    // Vložení nového uživatele do databáze
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        echo "Registrace byla úspěšná!";
    } else {
        echo "Chyba při registraci: " . $stmt->error;
    }

    // Zavření příkazů a připojení k databázi
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrace</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="index-nav">
        <ul>
            <li><a href="index.php" class="nav-link">Domů</a></li>
            <li><a href="game.php" class="nav-link">Hrát</a></li>
            <li><a href="login.php" class="nav-link">Přihlášení</a></li>
            <li><a href="register.php" class="nav-link">Registrace</a></li>
            <li><a href="rules.php" class="nav-link">Pravidla</a></li>
            <li><a href="about.php" class="nav-link">O nás</a></li>
        </ul>
    </nav>

    <h2 class="register-heading">Registrace uživatele</h2>

    <form action="register.php" method="POST" class="register-form">
        <label for="username">Uživatelské jméno:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Heslo:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="confirm_password">Potvrďte heslo:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>

        <input type="submit" value="Registrovat se" id="register-submit">

        <p>Máte účet? <a href="login.php">Přihlaste se zde</a></p>
    </form>
</body>
</html>
