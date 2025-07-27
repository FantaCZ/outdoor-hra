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

    // Hashování hesla pro bezpečnost (pro PHP 5.4 použijeme md5)
    $hashed_password = md5($password);

    // Ochrana proti SQL injection
    $username_escaped = mysqli_real_escape_string($conn, $username);

    // Kontrola, zda uživatelské jméno již existuje
    $sql = "SELECT id FROM users WHERE username = '$username_escaped'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        echo "Chyba dotazu: " . mysqli_error($conn);
        exit;
    }
    if (mysqli_num_rows($result) > 0) {
        echo "Toto uživatelské jméno již existuje!";
        exit;
    }

    // Vložení nového uživatele do databáze
    $sql = "INSERT INTO users (username, password) VALUES ('$username_escaped', '$hashed_password')";
    if (mysqli_query($conn, $sql)) {
        echo "Registrace byla úspěšná!";
    } else {
        echo "Chyba při registraci: " . mysqli_error($conn);
    }

    // Zavření připojení k databázi
    mysqli_close($conn);
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
<?php include 'navbar.php'; ?>
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
