<!DOCTYPE html>
<html lang="cs">

<head>
    <title>Úniková hra</title>
<!--     <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCr5JJsSMeRiuPePFZrlgYiStn-JRLwsl0&callback=initMap" async defer></script>
 -->

    <script src="./js/logika_hry.js"></script>

    <script>
        let map;
        let playerMarker;
        let inactivityTimeout; // Proměnná pro sledování nečinnosti
        const inactivityDuration = 15000; // 15 sekund nečinnosti před resetováním mapy
        let lastKnownPosition = { lat: 50.0903, lng: 14.4000 }; // Výchozí pozice hráče

        // Funkce pro inicializaci mapy
        function initMap() {
            console.log("Inicializace mapy...");

            // Výchozí nastavení mapy
            map = new google.maps.Map(document.getElementById("map"), {
                center: lastKnownPosition, // Výchozí souřadnice
                zoom: 14
            });

            // Načtení polohy hráče a pravidelná aktualizace mapy
            trackPlayerLocation();
            setInterval(() => updateMapPosition(), 15000); // Každých 15 sekund aktualizuj pozici

            // Přidání posluchačů pro interakce s mapou
            map.addListener("click", resetInactivityTimer);
            map.addListener("drag", resetInactivityTimer);
            map.addListener("zoom_changed", resetInactivityTimer);
        }

        // Funkce pro sledování polohy hráče
        function trackPlayerLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const playerLat = position.coords.latitude;
                        const playerLng = position.coords.longitude;

                        lastKnownPosition = { lat: playerLat, lng: playerLng }; // Uložení poslední známé pozice

                        // Nastavení mapy na pozici hráče
                        map.setCenter(lastKnownPosition);
                        map.setZoom(16); // Úroveň přiblížení

                        // Přidání markeru pro hráče, nebo jeho aktualizace
                        if (!playerMarker) {
                            playerMarker = new google.maps.Marker({
                                position: lastKnownPosition,
                                map: map,
                                title: "Vaše pozice"
                            });
                        } else {
                            playerMarker.setPosition(lastKnownPosition);
                        }

                        console.log("Poloha hráče:", playerLat, playerLng);
                    },
                    (error) => {
                        console.error("Chyba při získávání polohy:", error);
                    }
                );
            } else {
                alert("Geolokace není podporována tímto prohlížečem.");
            }
        }

        // Funkce pro aktualizaci pozice na mapě bez závislosti na pohybu
        function updateMapPosition() {
            if (lastKnownPosition && playerMarker) {
                // Aktualizuj střed mapy na poslední známou pozici a přiblížení
                map.setCenter(lastKnownPosition);
                map.setZoom(18); // Větší úroveň přiblížení

                console.log("Mapa byla automaticky zoomnuta a centrována na pozici hráče.");
            } else {
                console.warn("Poloha hráče není známá.");
            }
        }

        // Funkce pro resetování časovače nečinnosti
        function resetInactivityTimer() {
            clearTimeout(inactivityTimeout); // Zruší předchozí časovač

            // Nastaví nový časovač pro nečinnost
            inactivityTimeout = setTimeout(() => {
                console.log("Neaktivita detekována, resetování mapy...");
                updateMapPosition(); // Po 15 sekundách nečinnosti se automaticky zoomne na hráče
            }, inactivityDuration);
        }

    </script>

</head>
<body>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCr5JJsSMeRiuPePFZrlgYiStn-JRLwsl0&callback=initMap" async defer></script>

<link rel="stylesheet" href="./css/style.css">
<link rel="stylesheet" href="css/nav.css">
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

    <h1>Úniková hra</h1>

    <button onclick="submitAnswer()">Odeslat odpověď</button>

    <script>
        function submitAnswer() {
            const data = {
                question_id: 1,        // Tady zadej ID otázky
                answer: "Moje odpověď" // Tady zadej odpověď
            };

            const response = fetch('submit_answer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(data) // Převede objekt na URL-encoded string
            })
            console.log(response)
            // .then(response => response.json())
            // .then(result => console.log(result)) // Výpis odpovědi ze serveru
            // .catch(error => console.error('Error:', error));
        }
    </script>

    <div id="map" style="width: 100%; height: 500px;"></div>
    <div id="questionBox" style="display: block;">
        <p id="questionText"></p>
        <input type="text" id="answerInput">
        <button onclick="submitAnswer()">Odpovědět</button>
    </div>


</body>
</html>
