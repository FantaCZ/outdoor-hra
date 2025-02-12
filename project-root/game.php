<!DOCTYPE html>
<html lang="cs">
<head>
    <title>Úniková hra</title>
    <!-- Načítání Google Maps API s atributem async a defer pro správné načítání -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCr5JJsSMeRiuPePFZrlgYiStn-JRLwsl0&callback=initMap"></script>

    <!-- Tvůj vlastní JS soubor, který obsahuje funkci initMap -->
    <script src="project-root/js/logika_hry.js" defer></script>


    <script>
    function initMap() {
        console.log("Mapa byla zavolána.");
        const map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 50.0903, lng: 14.4000 },
            zoom: 14
        });
        console.log("Mapa byla vytvořena.");
    }
    </script>

</head>
<body>
    <h1>Úniková hra</h1>
    <div id="map" style="width: 100%; height: 500px;"></div>
    <div id="questionBox" style="display: none;">
        <p id="questionText"></p>
        <input type="text" id="answerInput">
        <button onclick="submitAnswer()">Odpovědět</button>
    </div>
</body>
</html>
