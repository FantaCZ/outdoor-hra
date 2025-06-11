<?php
session_start();
// Database connection (adjust credentials as needed)
$conn = new mysqli('localhost', 'root', '', 'escape_game');
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Získání celkového počtu otázek
$totalQuestions = 0;
$countResult = $conn->query("SELECT COUNT(*) AS total FROM questions");
if ($countRow = $countResult->fetch_assoc()) {
    $totalQuestions = (int)$countRow['total'];
}

// Inicializace počtu správných odpovědí v session
if (!isset($_SESSION['correct_answers'])) {
    $_SESSION['correct_answers'] = 0;
}

$wrongAnswer = false;
$questionRow = null;
$currentCorrectAnswer = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedAnswer = isset($_POST['answer']) ? trim($_POST['answer']) : '';
    $questionId = isset($_POST['question_id']) ? intval($_POST['question_id']) : 0;
    // Získat otázku a správnou odpověď podle ID
    $stmt = $conn->prepare("SELECT id, question_text, location_lat, location_lng, correct_answer, image_path FROM questions WHERE id = ?");
    $stmt->bind_param("i", $questionId);
    $stmt->execute();
    $stmt->bind_result($qid, $qtext, $qlat, $qlng, $correctAnswer, $imagePath);
    if ($stmt->fetch()) {
        $questionRow = [
            'id' => $qid,
            'question_text' => $qtext,
            'location_lat' => $qlat,
            'location_lng' => $qlng,
            'image_path' => $imagePath
        ];
        $currentCorrectAnswer = $correctAnswer;
        if (strcasecmp($submittedAnswer, $correctAnswer) !== 0) {
            $wrongAnswer = true;
            $showSuccess = false;
        } else {
            // Správná odpověď - přidej bod a přesměruj na novou otázku
            $_SESSION['correct_answers']++;
            header("Location: game1.php?success=1");
            exit;
        }
    }
    $stmt->close();
} else {
    // Fetch a random question with location
    $sql = "SELECT id, question_text, location_lat, location_lng, correct_answer, image_path FROM questions ORDER BY RAND() LIMIT 1";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $questionRow = $row;
        $currentCorrectAnswer = $row['correct_answer'];
    }
}

$imagePath = null;
if ($questionRow && isset($questionRow['image_path'])) {
    // Use the image_path column from the questions table
    $imagePath = $questionRow['image_path'];
}

// Fetch all questions with coordinates from the database
$questions = [];
$sql = "SELECT id, question_text, location_lat, location_lng FROM questions";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hra</title>
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        #map-container {
            width: 80%;
            height: 400px;
            margin: 20px auto;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.css" />
</head>
<body>
<div id="map-container"></div>
<button id="center-user-btn" style="position: absolute; top: 20px; right: 20px; z-index: 1000; padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">Centruj na mou polohu</button>
<button id="reset-map-btn" style="position: absolute; top: 60px; right: 20px; z-index: 1000; padding: 10px 20px; background: #f39c12; color: white; border: none; border-radius: 5px; cursor: pointer;">Resetovat mapu</button>
<script>
    const defaultCenter = [50.09297860, 14.40108390]; // Default center coordinates
    const defaultZoom = 14; // Default zoom level

    // Center map on default view when button is clicked
    document.getElementById('reset-map-btn').addEventListener('click', () => {
        map.setView(defaultCenter, defaultZoom);
    });

    // Initialize the map
    const map = L.map('map-container').setView(defaultCenter, defaultZoom); // Default center and zoom level

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Add markers for questions (red markers)
    const questions = <?php echo json_encode($questions); ?>;
    const redIcon = L.icon({
        iconUrl: 'assets/maps/red-icon.png', // Correct path to the red icon
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34]
    });
    questions.forEach(question => {
        const marker = L.marker([question.location_lat, question.location_lng], { icon: redIcon }).addTo(map);
        marker.bindPopup(`<strong>Otázka:</strong> ${question.question_text}`);
    });

    // Add user's location marker (custom blue icon)
    const blueIcon = L.icon({
        iconUrl: 'assets/maps/blue-icon.png', // Correct path to the blue icon
        iconSize: [30, 40], // Adjust size as needed
        iconAnchor: [15, 40], // Adjust anchor point
        popupAnchor: [0, -30] // Adjust popup position
    });
    let userLat, userLng;
    let userCircle; // Variable to store the circle

    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(
            (position) => {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;

                // Add or update the user's location marker
                if (window.userMarker) {
                    window.userMarker.setLatLng([userLat, userLng]);
                } else {
                    window.userMarker = L.marker([userLat, userLng], { icon: blueIcon }).addTo(map);
                    window.userMarker.bindPopup('<strong>Vaše poloha</strong>').openPopup();
                }

                // Add or update the circle around the user's location
                if (userCircle) {
                    userCircle.setLatLng([userLat, userLng]); // Ensure the circle is centered on the marker
                } else {
                    userCircle = L.circle([userLat, userLng], {
                        radius: 20, // Radius in meters
                        color: '#4CAF50',
                        fillColor: '#4CAF50',
                        fillOpacity: 0.2
                    }).addTo(map);
                }
            },
            (error) => {
                console.error('Chyba geolokace:', error);
            },
            { enableHighAccuracy: true }
        );
    } else {
        alert('Geolokace není podporována vaším prohlížečem.');
    }

    // Center map on user's location when button is clicked
    document.getElementById('center-user-btn').addEventListener('click', () => {
        if (userLat && userLng) {
            map.setView([userLat, userLng], 19); // Zoom level set to 19 for extreme close-up
        } else {
            alert('Vaše poloha není dostupná.');
        }
    });
</script>
<div id="question-container" style="max-width: 500px; margin: 40px auto; padding: 24px; border: 1px solid #ccc; border-radius: 8px; background: #f9f9f9;">
    <div id="question-text" style="font-size: 1.2em; margin-bottom: 16px;"><?php echo htmlspecialchars($questionRow['question_text']); ?></div>
    <form method="post">
        <input type="hidden" name="question_id" value="<?php echo $questionRow['id']; ?>">
        <input type="text" id="answer-input" name="answer" placeholder="Zadejte odpověď" style="width: 100%; padding: 8px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 4px;">
        <button id="answer-btn" type="submit" style="padding: 8px 16px;">Potvrdit</button>
        <?php if ($wrongAnswer): ?>
            <div class="wrong-answer-msg" style="color: #d32f2f; margin-top: 8px; font-weight: bold;">Špatná odpověď</div>
        <?php endif; ?>
    </form>
    <button class="show-answer-btn" type="button" onclick="showCorrectAnswer()" style="margin-top: 12px; padding: 6px 16px; background: #2980d9; color: #fff; border: none; border-radius: 5px; font-size: 1em; cursor: pointer; transition: background 0.2s;">Zobrazit správnou odpověď</button>
    <div id="correct-answer" style="display:none;">
        <span class="correct-answer-msg" style="margin-top: 10px; color: #388e3c; font-weight: bold; background: #e8f5e9; padding: 8px 12px; border-radius: 5px; display: inline-block;"><?php echo htmlspecialchars($currentCorrectAnswer); ?></span>
    </div>
    <script>
        function showCorrectAnswer() {
            document.getElementById('correct-answer').style.display = 'block';
        }
    </script>
    <div class="status-panel" style="margin-top:30px; padding:16px; background:#e8f5e9; border-radius:8px; text-align:center; font-size:1.1em;">
        <strong>Správně zodpovězeno:</strong> <?php echo $_SESSION['correct_answers']; ?> /
        <strong>Zbývá:</strong> <?php echo max(0, $totalQuestions - $_SESSION['correct_answers']); ?>
    </div>
</div>
</body>
</html>
<?php
$conn->close();
?>
