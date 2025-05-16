<?php
// Připojení k databázi
include('db.php');

// Ověření, zda je formulář odeslán
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Ověření, že uživatelské jméno a heslo nejsou prázdné
    if (empty($username) || empty($password)) {
        $error_message = "Uživatelské jméno a heslo jsou povinné!";
    } else {
        // Příprava dotazu pro kontrolu uživatelského jména
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        

        // Pokud uživatel existuje, ověříme heslo
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $db_username, $db_password);
            $stmt->fetch();

            // Ověření hesla
            if (password_verify($password, $db_password)) {
                // Přihlášení úspěšné - nastavíme session a přesměrujeme
                session_start();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $db_username;
                header("Location: dashboard.php"); // Přesměrování na dashboard po přihlášení
                exit;
            } else {
                $error_message = "Nesprávné uživatelské jméno nebo heslo!";
            }
        } else {
            $error_message = "Uživatelské jméno neexistuje!";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihlášení</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/nav.css">
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
<h2>Přihlášení</h2>
<?php
if (isset($error_message)) {
    echo "<p class='error'>$error_message</p>";
}
?>
<form action="login.php" method="POST" class="login-form">
    <label for="username">Uživatelské jméno:</label>
    <input type="text" id="username" name="username" required><br><br>

    <label for="password">Heslo:</label>
    <input type="password" id="password" name="password" required><br><br>

    <input type="submit" value="Přihlásit se">
</form>
<p>Nemáte účet? <a href="register.php">Registrujte se zde</a></p>
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
