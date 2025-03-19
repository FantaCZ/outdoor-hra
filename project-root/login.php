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
</head>
<body>
    typico tk
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
    </div>
</body>
</html>
