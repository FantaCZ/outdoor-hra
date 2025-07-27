<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/nav.css">
        <title>navbar</title>
    </head>
    <body>
        <button class="nav-open-btn" id="navOpenBtn" aria-label="Otevřít menu" style="display:block;">
            Otevřít menu
        </button>

        <div class="nav-overlay" id="navOverlay"></div>

        <nav class="index-nav" id="mainNav" style="display:none;">
            <ul>
                <li><a href="index.php" class="nav-link">Domů</a></li>
                <li><a href="game1.php" class="nav-link">Hrát</a></li>
                <li><a href="login.php" class="nav-link">Přihlášení</a></li>
                <li><a href="register.php" class="nav-link">Registrace</a></li>
                <li><a href="rules.php" class="nav-link">Pravidla</a></li>
                <li><a href="about.php" class="nav-link">O nás</a></li>
                <li><button type="button" class="nav-close-btn" id="navCloseBtn">Zavřít</button></li>
            </ul>
        </nav>
    </body>
</html>
