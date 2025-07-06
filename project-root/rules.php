<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pravidla hry</title>
    <link rel="stylesheet" href="css/nav.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f3;
            margin: 0;
            padding: 0;
        }
        .rules-section {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 800px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .rules-section h1 {
            text-align: center;
            color: #333;
        }
        .rules-section ul {
            list-style-type: none;
            padding: 0;
        }
        .rules-section ul li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .rules-section ul li:last-child {
            border-bottom: none;
        }
        @media (max-width: 600px) {
            .index-nav ul li {
                display: block;
                margin: 10px 0;
            }
            .rules-section {
                padding: 15px;
                margin: 10px;
            }
            .rules-section ul li {
                padding: 8px;
            }
        }
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
    <section class="rules-section">
        <h1>Pravidla hry</h1>
        <ul>
            <li>Pravidlo 1: Hráči musí dodržovat všechny pokyny organizátorů.</li>
            <li>Pravidlo 2: Hra je určena pro týmy o 2-5 hráčích.</li>
            <li>Pravidlo 3: Hráči musí být starší 18 let.</li>
            <li>Pravidlo 4: Používání mobilních telefonů je povoleno pouze pro navigaci.</li>
            <li>Pravidlo 5: Hra končí, když tým dosáhne cíle nebo uplyne časový limit.</li>
        </ul>
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
