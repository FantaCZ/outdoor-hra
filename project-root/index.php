<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hlavní stránka</title>
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body.index-page {
            background-image: url('assets/1uzpp.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }
        .index-wrapper {
            background: rgba(255,255,255,0.85);
            border-radius: 16px;
            padding: 2rem;
            max-width: 600px;
            margin: 2rem auto;
            box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        }
        /* Přidej styl pro fialové odkazy v index-wrapper */
        .index-wrapper a {
            color: #800080;
            text-decoration: underline;
        }
        .index-wrapper a:hover {
            color: #a020f0;
        }
    </style>
</head>
<body class="index-page">
    <?php include 'navbar.php'; ?>
    <div class="index-wrapper">
        <h1>Rád bych vás přivítal ve hře</h1>
        <h1 style="color: #145a32;">Únik z Pražské pasti</h1>
        <p>Pro více informací si můžete prohlédnout naše pravidla nebo se dozvědět více <a href="about.php">o nás</a>.</p>
        <p>Pro spuštění hry se musíte <a href="login.php">přihlásit zde</a>.</p>
        <p>Pokud jste se přihlásili, můžete kliknout zde pro přesměrování na <a href="game1.php">samotnou hru</a>.</p>
    </div>

    <!-- Fotky -->
    <section class="photo-gallery">
        <h2>Jaké místa obsahuje tato hra?</h2>
        <div class="photos">
            <img src="assets/praha_main.png" alt="Photo 1" onclick="openModal(this)">
            <img src="assets/mala_strana.jpg" alt="Photo 2" onclick="openModal(this)">
            <img src="assets/letna.jpg" alt="Photo 3" onclick="openModal(this)">
        </div>
    </section>

    <!-- Modal pro zvětšený obrázek -->
    <div id="myModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-content">
            <img id="modalImage" src="">
        </div>
    </div>

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
