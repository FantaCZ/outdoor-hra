<?php include "db.php"; ?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <title>Úniková hra</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCr5JJsSMeRiuPePFZrlgYiStn-JRLwsl0&async defer></script>
    <script src="js/logika_hry.js" defer></script>

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
