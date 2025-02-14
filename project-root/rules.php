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
    </style>
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
</body>
</html>