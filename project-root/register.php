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
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .nav-open-btn, .nav-close-btn {
            background: #229954;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            padding: 8px 18px;
            margin: 8px 0;
            cursor: pointer;
            transition: background 0.2s;
            font-family: 'Segoe UI', 'Roboto', 'Arial', 'Helvetica Neue', sans-serif;
            letter-spacing: 2px;
        }
        .nav-open-btn:hover, .nav-close-btn:hover {
            background: #28b463;
        }
        .nav-close-btn {
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<button class="nav-open-btn" id="navOpenBtn" aria-label="Otevřít menu" style="display:block;">
    Otevřít menu
</button>
<div class="nav-overlay" id="navOverlay"></div>
<nav class="index-nav" id="mainNav" style="display:none;">
    <ul>
        <li><a href="index.php" class="nav-link">Domů</a></li>
        <li><a href="game.php" class="nav-link">Hrát</a></li>
        <li><a href="login.php" class="nav-link">Přihlášení</a></li>
        <li><a href="register.php" class="nav-link">Registrace</a></li>
        <li><a href="rules.php" class="nav-link">Pravidla</a></li>
        <li><a href="about.php" class="nav-link">O nás</a></li>
        <li><button type="button" class="nav-close-btn" id="navCloseBtn">Zavřít</button></li>
    </ul>
</nav>
<br><br>
<h2 class="register-heading">Registrace</h2>
<form action="register.php" method="post" class="register-form">
    <label for="username">Uživatelské jméno:</label>
    <input type="text" id="register-username" name="username" required><br><br>
    <label for="password">Heslo:</label>
    <input type="password" id="register-password" name="password" required><br><br>
    <label for="confirm_password">Potvrzení hesla:</label>
    <input type="password" id="register-confirm-password" name="confirm_password" required><br><br>
    <input type="submit" id="register-submit" value="Registrovat">
</form>
<script>
    const nav = document.getElementById('mainNav');
    const overlay = document.getElementById('navOverlay');
    const navOpenBtn = document.getElementById('navOpenBtn');
    const navCloseBtn = document.getElementById('navCloseBtn');
    function openNav() {
        nav.style.display = 'block';
        overlay.classList.add('active');
        navOpenBtn.style.display = 'none';
    }
    function closeNav() {
        nav.style.display = 'none';
        overlay.classList.remove('active');
        navOpenBtn.style.display = 'block';
    }
    navOpenBtn.addEventListener('click', openNav);
    navCloseBtn.addEventListener('click', closeNav);
    overlay.addEventListener('click', closeNav);
    function handleResize() {
        if (window.innerWidth <= 768) {
            closeNav();
        } else {
            nav.style.display = 'block';
            overlay.classList.remove('active');
            navOpenBtn.style.display = 'none';
        }
    }
    handleResize();
    window.addEventListener('resize', handleResize);
</script>
</body>
</html>