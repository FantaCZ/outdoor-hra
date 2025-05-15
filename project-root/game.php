<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Úniková hra</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="css/nav.css">
    <script src="./js/logika_hry.js"></script>
</head>
<body class="game-page">

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
<br>
    <div class="game-container">
        <h1>Úniková hra</h1>
        
        <div id="questionBox" style="display: block;">
            <p id="questionText">Zde se zobrazí otázka</p>
            <input type="text" id="answerInput" placeholder="Zadejte odpověď...">
            <button onclick="submitAnswer()">Odpovědět</button>
        </div>
    </div>

    <script>
        function submitAnswer() {
            const data = {
                question_id: 1,        // Tady zadej ID otázky
                answer: "Moje odpověď" // Tady zadej odpověď
            };

            const response = fetch('./api/submit_answer.php', { // Opravená cesta
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(data) // Převede objekt na URL-encoded string
            });
            console.log(response)
             .then(response => response.json())
             .then(result => console.log(result)) // Výpis odpovědi ze serveru
             .catch(error => console.error('Error:', error));
        }
    </script>

<!-- Zobrazení GPS souřadnic uživatele -->
<div id="gps-coords" style="margin-top:10px; font-size:1.1em;">
    <strong>Vaše poloha:</strong>
    <span id="user-lat">Načítám...</span>, <span id="user-lng"></span>
</div>
<script>
    // Získání GPS souřadnic uživatele a zobrazení pod mapou
    function showUserGPS() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('user-lat').textContent = position.coords.latitude.toFixed(6);
                document.getElementById('user-lng').textContent = position.coords.longitude.toFixed(6);
            }, function() {
                document.getElementById('user-lat').textContent = "Nelze zjistit polohu";
                document.getElementById('user-lng').textContent = "";
            });
        } else {
            document.getElementById('user-lat').textContent = "Geolokace není podporována";
            document.getElementById('user-lng').textContent = "";
        }
    }
    showUserGPS();
</script>

</body>
</html>
