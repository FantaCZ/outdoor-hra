<?php
session_start();
// Připojení k databázi
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

$userId = $_SESSION['user_id'];
$questions = getQuestionsForUser($conn, $userId);

//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    $submittedAnswer = isset($_POST['answer']) ? trim($_POST['answer']) : '';
//    $questionId = isset($_POST['question_id']) ? intval($_POST['question_id']) : 0;
//    // Získat otázku a správnou odpověď podle ID
//    $stmt = $conn->prepare("SELECT id, question_text, location_lat, location_lng, correct_answer, image_path FROM questions WHERE id = ?");
//    $stmt->bind_param("i", $questionId);
//    $stmt->execute();
//    $stmt->bind_result($qid, $qtext, $qlat, $qlng, $correctAnswer, $imagePath);
//    if ($stmt->fetch()) {
//        $questionRow = [
//            'id' => $qid,
//            'question_text' => $qtext,
//            'location_lat' => $qlat,
//            'location_lng' => $qlng,
//            'image_path' => $imagePath
//        ];
//        $currentCorrectAnswer = $correctAnswer;
//        if (strcasecmp($submittedAnswer, $correctAnswer) !== 0) {
//            $wrongAnswer = true;
//            $showSuccess = false;
//        } else {
//            // Správná odpověď - přidej bod a přesměruj na novou otázku
//            $_SESSION['correct_answers']++;
//            header("Location: game1.php?success=1");
//            exit;
//        }
//    }
//    $stmt->close();
//}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $answer = isset($_POST['answer']) ? $_POST['answer'] : '';
    $questionId = isset($_POST['question_id']) ? $_POST['question_id'] : null;

    // Validate inputs
    if (!empty($answer) && !empty($questionId)) {
        handleAnswer( $conn,$userId, $questionId, $answer);
    } else {
        echo "<script>console.error('no answer');</script>";
    }
}

function handleAnswer( $conn,$userId, $questionId, $answer) {
    echo "<script>console.log('$questionId');</script>";
    echo "<script>console.log('$answer');</script>";

    $stmt = $conn->prepare("SELECT correct_answer FROM questions WHERE id = ?");
    $stmt->bind_param("i", $questionId);
    $stmt->execute();
    $result = $stmt->get_result();
//    echo "<script>console.log('$result');</script>";

    if ($row = $result->fetch_assoc()) {
        $correctAnswer = $row['correct_answer'];

        // Compare trimmed lowercase strings
        if (trim(strtolower($answer)) === trim(strtolower($correctAnswer))) {
            echo "<script>console.log('Correct');</script>";
            $pid = getUserProgressId($conn, $userId);
            markQuestionAsAnswered($conn, $pid, $questionId);

            $progressFinished = areAllQuestionsAnswered($conn, $pid);
            echo "<script>console.log(" . json_encode($progressFinished) . ");</script>";

            if ($progressFinished) {
                // TODO save time score to progress table
            }
        }
    } else {
        echo "<script>console.log('Question not found');</script>";
    }

    $stmt->close();
}

function getUserProgressId($conn, $userId) {
    $stmt = $conn->prepare("SELECT progress_id FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['progress_id']; // could be null if not set
    }

    return null; // user not found
}

function markQuestionAsAnswered($conn, $progressId, $questionId) {
    $stmt = $conn->prepare("
        UPDATE progress_questions 
        SET answered = 1 
        WHERE progress_id = ? AND question_id = ?
    ");
    $stmt->bind_param("ii", $progressId, $questionId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>console.log('Marked as answered');</script>";
    } else {
        echo "<script>console.log('No matching row found');</script>";
    }

    $stmt->close();
}

function areAllQuestionsAnswered($conn, $progressId) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS unanswered_count 
        FROM progress_questions 
        WHERE progress_id = ? AND answered = 0
    ");
    $stmt->bind_param("i", $progressId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['unanswered_count'] == 0;
    }

    return false; // fallback if progress ID not found
}

function getQuestionsForUser($conn, $userId) {
    $userHasProgress = hasUserProgress($conn, $userId);
    $questions = [];

   if ($userHasProgress) {
       // load user's questions
       $sql = "
            SELECT q.id, q.question_text, q.location_lat, q.location_lng, q.correct_answer, q.image_path
            FROM users u
            JOIN progress_questions pq ON u.progress_id = pq.progress_id
            JOIN questions q ON pq.question_id = q.id
            WHERE u.id = ? AND u.progress_id IS NOT NULL
        ";

       $stmt = $conn->prepare($sql);
       $stmt->bind_param("i", $userId);
       $stmt->execute();
       $result = $stmt->get_result();

       while ($row = $result->fetch_assoc()) {
           $questions[] = $row;
       }
   } else {
       // load random questions
       $sql = "SELECT id, question_text, location_lat, location_lng, correct_answer, image_path
        FROM questions
        ORDER BY RAND()
        LIMIT 10";
       $result = $conn->query($sql);
       while ($row = $result->fetch_assoc()) {
           $questions[] = $row;
       }

       // new progress row
       $conn->query("INSERT INTO progress () VALUES ()");
       $progressId = $conn->insert_id;

       // insert into progress_questions
       $insertStmt = $conn->prepare("
            INSERT INTO progress_questions (progress_id, question_id) 
            VALUES (?, ?)
        ");

       foreach ($questions as $row) {
           $insertStmt->bind_param("ii", $progressId, $row['id']);
           $insertStmt->execute();
       }
       $insertStmt->close();

       // update user with this progress_id
       $update = $conn->prepare("UPDATE users SET progress_id = ? WHERE id = ?");
       $update->bind_param("ii", $progressId, $userId);
       $update->execute();
       $update->close();
   }

   return $questions;
}

function hasUserProgress($conn, $userId) {
    $stmt = $conn->prepare("SELECT progress_id FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (is_null($row['progress_id'])) {
            return false;
        } else {
            return true;
        }
    } else {
        echo "<script>console.error('user not found: $userId');</script>";
    }
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

        // --- OPRAVA: Funkce pro tlačítka ---
        document.getElementById('center-user-btn').addEventListener('click', function () {
            if (window.userMarker) {
                map.setView(window.userMarker.getLatLng(), 17, { animate: true });
            } else {
                // Pokud není známá poloha, zkusit získat polohu uživatele
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        map.setView([position.coords.latitude, position.coords.longitude], 17, { animate: true });
                    });
                }
            }
        });

        document.getElementById('reset-map-btn').addEventListener('click', function () {
            map.setView(defaultCenter, defaultZoom, { animate: true });
        });
    });

    // --- Časomíra ---
    let timerInterval;
    let elapsedSeconds = 0;

    // Načti čas z localStorage, pokud existuje (pro případ obnovení stránky)
    if (localStorage.getItem('game_timer')) {
        elapsedSeconds = parseInt(localStorage.getItem('game_timer'), 10) || 0;
    }

    function startTimer() {
        timerInterval = setInterval(() => {
            elapsedSeconds++;
            localStorage.setItem('game_timer', elapsedSeconds);
            updateTimerDisplay();
        }, 1000);
    }

    function stopTimer() {
        clearInterval(timerInterval);
    }

    function updateTimerDisplay() {
        let minutes = Math.floor(elapsedSeconds / 60);
        let seconds = elapsedSeconds % 60;
        document.getElementById('timer').innerText =
            `${minutes}:${seconds.toString().padStart(2, '0')}`;
    }

    // Přidej časomíru do stránky
    const timerDiv = document.createElement('div');
    timerDiv.id = 'timer';
    timerDiv.style = 'position:fixed;top:10px;right:10px;background:#fff;padding:8px 16px;border-radius:8px;box-shadow:0 2px 8px #0002;font-size:1.5em;z-index:1000;font-weight:bold;color:#d32f2f;letter-spacing:1px;border:2px solid #d32f2f;';
    document.body.appendChild(timerDiv);
    updateTimerDisplay();
    startTimer();

    // --- Odeslání času do PHP po dokončení hry ---
    function sendTimeToServer() {
        fetch('save_progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ elapsed_seconds: elapsedSeconds })
        });
    }

    // --- Zákaz zavření/obnovení stránky před dokončením hry ---
    window.addEventListener('beforeunload', function (e) {
        // Zjisti počet správných odpovědí z PHP proměnné
        const correctAnswers = <?php echo (int)$_SESSION['correct_answers']; ?>;
        const totalQuestions = <?php echo (int)$totalQuestions; ?>;
        if (correctAnswers < totalQuestions) {
            e.preventDefault();
            e.returnValue = 'Hru nelze opustit, dokud nezodpovíte všechny otázky!';
            return 'Hru nelze opustit, dokud nezodpovíte všechny otázky!';
        } else {
            // Po dokončení hry odešli čas na server
            sendTimeToServer();
            localStorage.removeItem('game_timer');
        }
    });

    // Po odeslání odpovědi zkontroluj, zda je hra dokončena
    //document.querySelector('form').addEventListener('submit', function () {
    //    const correctAnswers = <?php //echo (int)$_SESSION['correct_answers']; ?>//;
    //    const totalQuestions = <?php //echo (int)$totalQuestions; ?>//;
    //    if (correctAnswers + 1 >= totalQuestions) { // +1 protože odpověď se teprve zpracuje
    //        stopTimer();
    //        sendTimeToServer();
    //        localStorage.removeItem('game_timer');
    //    }
    //});

    // --- Odeslání postupu do backendu ---
    function sendProgress(userId, username, answeredCorrectly, elapsedTime) {
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('username', username);
        formData.append('answered_correctly', answeredCorrectly);
        formData.append('completion_time', elapsedTime);

        fetch('game_logic.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // případně zobrazit potvrzení
            console.log(data);
        })
        .catch(error => {
            console.error('Chyba při ukládání progressu:', error);
        });
    }

    // Příklad použití: zavolejte tuto funkci při dokončení úkolu/hry
    // sendProgress(4, 'root', 1, '00:12:34'); // user_id, username, správně odpovězeno, čas
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
