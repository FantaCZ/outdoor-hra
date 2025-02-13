<!DOCTYPE html>
<html lang="cs">

<head>
    <title>Úniková hra</title>
<!--     <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCr5JJsSMeRiuPePFZrlgYiStn-JRLwsl0&callback=initMap" async defer></script>
 -->

 
    <script src="/outdoor-hra/project-root/js/logika_hry.js"></script>

    
    <script>
    function initMap() {
        console.log("Mapa byla zavolána.");
        const map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 50.0903, lng: 14.4000 },
            zoom: 14
        });
        console.log("Mapa byla vytvořena.");
    }

                // Načíst otázky z API nebo souboru
                fetch('api/fetch_questions.php') // Tento endpoint by měl vracet JSON s otázkami a souřadnicemi
                .then(response => response.json())
                .then(data => {
                    // Pro každou otázku přidáme marker na mapu
                    data.forEach(question => {
                        // Ujistěte se, že každá otázka má lat a lng
                        if (question.lat && question.lng) {
                            const marker = new google.maps.Marker({
                                position: { lat: question.lat, lng: question.lng },
                                map: map,
                                title: question.question // Titulek je text otázky
                            });

                            // Volitelně: Zobrazení detaily otázky při kliknutí na marker
                            const infoWindow = new google.maps.InfoWindow({
                                content: `<h3>${question.question}</h3>`
                            });

                            marker.addListener('click', function() {
                                infoWindow.open(map, marker);
                            });
                        }
                    });
                })
                .catch(error => console.error('Chyba při načítání otázek:', error));
            </script>

</head>
<body>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCr5JJsSMeRiuPePFZrlgYiStn-JRLwsl0&callback=initMap" async defer></script>

<link rel="stylesheet" href="/outdoor-hra/project-root/css/style.css">
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

            fetch('submit_answer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(data) // Převede objekt na URL-encoded string
            })
            .then(response => response.json())
            .then(result => console.log(result)) // Výpis odpovědi ze serveru
            .catch(error => console.error('Error:', error));
        }
    </script>

    <div id="map" style="width: 100%; height: 500px;"></div>
    <div id="questionBox" style="display: yes;">
        <p id="questionText"></p>
        <input type="text" id="answerInput">
        <button onclick="submitAnswer()">Odpovědět</button>
    </div>
    


</body>
</html>
