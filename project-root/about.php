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
        .about-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 60vh;
        }
        .about-section img {
            margin-top: 20px;
            border-radius: 50%;
            box-shadow: 0 4px 16px rgba(34,153,84,0.15);
        }
        body.about-page {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
    </style>
</head>
<body class="about-page">
    <?php include 'navbar.php'; ?>
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
