<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2ecc71">
    <title>About Us</title>
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
<body class="about-page">
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
    <section class="about-section">
        <h2>O mně</h2>
        <p>Jsem nadšený vývojář, který miluje vytváření interaktivních her. Mým cílem je přinášet zábavu a vzdělávání prostřednictvím inovativních herních zážitků.</p>
        <img src="assets/profile.jpg" alt="Profile Photo" style="width: 200px; height: auto;">
    </section>
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