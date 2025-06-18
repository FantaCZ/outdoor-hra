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
}

// Fetch all questions with coordinates from the database
$questions = [];
$sql = "SELECT id, question_text, location_lat, location_lng, correct_answer, image_path FROM questions";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

// Pass all questions to JavaScript for proximity calculation
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
        body {
            background: url('assets/maps/logo.png') repeat;
            background-size: 50px 50px; /* Small tile size */
        }
        #map-container {
            width: 80%;
            height: 400px;
            margin: 20px auto;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.8); /* Semi-transparent background */
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.css" />
</head>
<body>
<div id="map-container"></div>
<div style="display: flex; justify-content: center; gap: 10px; margin: 20px 0;">
    <button id="center-user-btn" style="padding: 10px 20px; background: #2980d9; color: white; border: none; border-radius: 5px; cursor: pointer;">Centruj na mou polohu</button>
    <button id="reset-map-btn" style="padding: 10px 20px; background: #d32f2f; color: white; border: none; border-radius: 5px; cursor: pointer;">Resetovat mapu</button>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const defaultCenter = [50.09297860, 14.40108390]; // Default center coordinates
        const defaultZoom = 14; // Default zoom level

        // Define map boundaries
        const bounds = L.latLngBounds(
            [50.05, 14.35], // Southwest corner
            [50.15, 14.45]  // Northeast corner
        );

        const map = L.map('map-container', {
            maxBounds: bounds,
            maxBoundsViscosity: 1.0
        }).setView(defaultCenter, defaultZoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const questions = <?php echo json_encode($questions); ?>;
        const redIcon = L.icon({
            iconUrl: 'assets/maps/red-icon.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });

        questions.forEach(question => {
            const marker = L.marker([question.location_lat, question.location_lng], { icon: redIcon }).addTo(map);
            marker.bindPopup(`<strong>Otázka:</strong> ${question.question_text}`);
        });

        const blueIcon = L.icon({
            iconUrl: 'assets/maps/blue-icon.png',
            iconSize: [30, 40],
            iconAnchor: [15, 40],
            popupAnchor: [0, -30]
        });

        let userLat, userLng;
        let userCircle;
        const questionContainer = document.getElementById('question-container');
        const questionText = document.getElementById('question-text');
        const questionIdInput = document.querySelector('input[name="question_id"]');

        // Debugging: Check if questionContainer exists
        if (!questionContainer) {
            console.error('Element with id "question-container" not found in the DOM.');
        }

        function calculateDistance(lat1, lng1, lat2, lng2) {
            const R = 6371e3; // Earth's radius in meters
            const φ1 = lat1 * Math.PI / 180;
            const φ2 = lat2 * Math.PI / 180;
            const Δφ = (lat2 - lat1) * Math.PI / 180;
            const Δλ = (lng2 - lng1) * Math.PI / 180;

            const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                      Math.cos(φ1) * Math.cos(φ2) *
                      Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return R * c; // Distance in meters
        }

        function findClosestQuestion(userLat, userLng) {
            let closestQuestion = null;
            let minDistance = Infinity;

            questions.forEach(question => {
                const distance = calculateDistance(userLat, userLng, question.location_lat, question.location_lng);
                if (distance < minDistance) {
                    minDistance = distance;
                    closestQuestion = question;
                }
            });

            return { closestQuestion, minDistance };
        }

        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                (position) => {
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;

                    // Update user's location marker
                    if (window.userMarker) {
                        window.userMarker.setLatLng([userLat, userLng]);
                    } else {
                        window.userMarker = L.marker([userLat, userLng], { icon: blueIcon }).addTo(map);
                        window.userMarker.bindPopup('<strong>Vaše poloha</strong>').openPopup();
                    }

                    // Update or create the circle around the user's location
                    if (userCircle) {
                        userCircle.setLatLng([userLat, userLng]);
                    } else {
                        userCircle = L.circle([userLat, userLng], {
                            radius: 20,
                            color: '#4CAF50',
                            fillColor: '#4CAF50',
                            fillOpacity: 0.2
                        }).addTo(map);
                    }

                    // Find the closest question
                    const { closestQuestion, minDistance } = findClosestQuestion(userLat, userLng);
                    console.log(`Closest question distance: ${minDistance} meters`);

                    if (closestQuestion && minDistance <= 20) {
                        if (questionContainer) {
                            // Update the question container with the closest question
                            questionContainer.style.display = 'block';
                            questionText.innerText = closestQuestion.question_text;
                            questionIdInput.value = closestQuestion.id;
                        } else {
                            console.error('Question container is not available.');
                        }
                    } else {
                        if (questionContainer) {
                            questionContainer.style.display = 'none';
                        }
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

        // Ensure the container is hidden by default
        if (questionContainer) {
            questionContainer.style.display = 'none';
        }
    });
</script>
<div id="question-container" style="max-width: 500px; margin: 40px auto; padding: 24px; border: 1px solid #ccc; border-radius: 8px; background: #f9f9f9; display: none;">
    <div id="question-text" style="font-size: 1.2em; margin-bottom: 16px;"></div>
    <form method="post">
        <input type="hidden" name="question_id" value="">
        <input type="text" id="answer-input" name="answer" placeholder="Zadejte odpověď" style="width: 100%; padding: 8px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 4px;">
        <button id="answer-btn" type="submit" style="padding: 8px 16px;">Potvrdit</button>
    </form>
</div>
</body>
</html>
<?php
$conn->close();
?>
