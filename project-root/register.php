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
    <link rel="stylesheet" href="css/nav.css"> <!-- Added nav.css -->
    <link rel="stylesheet" href="css/style.css"> <!-- Added style.css -->
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
    <br><br>
    <h2 class="register-heading">Registrace</h2> <!-- Added class "register-heading" -->
    <form action="register.php" method="post" class="register-form"> <!-- Added class "register-form" -->
        <label for="username">Uživatelské jméno:</label>
        <input type="text" id="register-username" name="username" required><br><br> <!-- Added id "register-username" -->
        <label for="password">Heslo:</label>
        <input type="password" id="register-password" name="password" required><br><br> <!-- Added id "register-password" -->
        <label for="confirm_password">Potvrzení hesla:</label>
        <input type="password" id="register-confirm-password" name="confirm_password" required><br><br> <!-- Added id "register-confirm-password" -->
        <input type="submit" id="register-submit" value="Registrovat"> <!-- Added id "register-submit" -->
    </form>
</body>
</html>