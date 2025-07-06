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
            // Prevent moving to the next question
            // header("Location: game.php?success=0");
            // exit;
        } else {
            // Správná odpověď - přidej bod a přesměruj na novou otázku
            $_SESSION['correct_answers']++;
            header("Location: game.php?success=1");
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

if ($questionRow) {
    // Pokud je požadavek AJAX (např. fetch), vrať JSON
    if (
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        header('Content-Type: application/json');
        echo json_encode([
            'id' => $questionRow['id'],
            'question' => $questionRow['question_text'],
            // 'allowed_distance' => 50, // Zakomentováno dle požadavku
            'latitude' => floatval($questionRow['location_lat']),
            'longitude' => floatval($questionRow['location_lng'])
        ]);
    } else {
        // Pokud je v URL success=1, zobraz overlay
        $showSuccess = isset($_GET['success']) && $_GET['success'] == '1';
        ?>
        <!DOCTYPE html>
        <html lang="cs">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Otázka</title>
            <link rel="stylesheet" href="css/nav.css">
            <style>
                        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('assets/gametheme.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }
                #question-container {
                    max-width: 500px;
                    margin: 40px auto;
                    padding: 24px;
                    border: 1px solid #ccc;
                    border-radius: 8px;
                    background: #f9f9f9;
                }
                #question-text {
                    font-size: 1.2em;
                    margin-bottom: 16px;
                }
                #answer-input {
                    width: 100%;
                    padding: 8px;
                    margin-bottom: 12px;
                }
                #answer-btn {
                    padding: 8px 16px;
                }
                .wrong-answer-msg {
                    color: #d32f2f;
                    margin-top: 8px;
                    font-weight: bold;
                }
                .success-overlay {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    background: rgba(46, 204, 113, 0.97);
                    color: #fff;
                    font-size: 3em;
                    font-weight: bold;
                    z-index: 9999;
                    transition: opacity 0.3s;
                    cursor: pointer;
                    font-family: 'Segoe UI', 'Roboto', 'Arial', 'Helvetica Neue', sans-serif;
                    letter-spacing: 2px;
                }
                .show-answer-btn {
                    margin-top: 12px;
                    padding: 6px 16px;
                    background: #2980d9;
                    color: #fff;
                    border: none;
                    border-radius: 5px;
                    font-size: 1em;
                    cursor: pointer;
                    transition: background 0.2s;
                }
                .show-answer-btn:hover {
                    background: #1c5fa8;
                }
                .correct-answer-msg {
                    margin-top: 10px;
                    color: #388e3c;
                    font-weight: bold;
                    background: #e8f5e9;
                    padding: 8px 12px;
                    border-radius: 5px;
                    display: inline-block;
                }
                .status-panel {
                    margin-top:30px;
                    padding:16px;
                    background:#e8f5e9;
                    border-radius:8px;
                    text-align:center;
                    font-size:1.1em;
                }
                .nav-toggle-btn {
                    position: fixed;
                    top: 10px;
                    left: 10px;
                    z-index: 1000;
                    background: transparent;
                    border: none;
                    cursor: pointer;
                }
                .nav-toggle-btn span {
                    display: block;
                    width: 30px;
                    height: 3px;
                    background: #333;
                    margin: 5px 0;
                }
                .nav-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    display: none;
                }
                .nav-overlay.active {
                    display: block;
                }
                .nav-open {
                    transform: translateX(0);
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
                }
                .nav-open-btn:hover, .nav-close-btn:hover {
                    background: #28b463;
                }
                .nav-close-btn {
                    width: 100%;
                    margin-top: 10px;
                }
                @media screen and (max-width: 768px) {
                    .nav-open-btn {
                        display: block;
                        position: fixed;
                        top: 12px;
                        left: 12px;
                        z-index: 1200;
                    }

                    .nav-close-btn {
                        display: block;
                    }
                }
            </style>
        </head>
        <body>
        <?php include 'navbar.php'; ?>
        <?php if ($imagePath): ?>
            <div id="question-image-container" style="text-align:center; margin-bottom:20px;">
                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Question Image" style="width:100%; max-width:500px; height:auto; border-radius:8px;">
            </div>
        <?php endif; ?>
        <div id="question-container">
            <div id="question-text"><?php echo htmlspecialchars($questionRow['question_text']); ?></div>
                <form method="post">
                    <input type="hidden" name="question_id" value="<?php echo $questionRow['id']; ?>">
                    <input type="text" id="answer-input" name="answer" placeholder="Zadejte odpověď">
                    <button id="answer-btn" type="submit">Potvrdit</button>
                    <?php if ($wrongAnswer): ?>
                        <div class="wrong-answer-msg">Špatná odpověď</div>
                    <?php endif; ?>
                </form>
                <button class="show-answer-btn" type="button" onclick="showCorrectAnswer()">Zobrazit správnou odpověď</button>
                <div id="correct-answer" style="display:none;">
                    <span class="correct-answer-msg"><?php echo htmlspecialchars($currentCorrectAnswer); ?></span>
                </div>
                <script>
                    function showCorrectAnswer() {
                        document.getElementById('correct-answer').style.display = 'block';
                    }
                </script>
                <!-- Stavový panel -->
                <div class="status-panel">
                    <strong>Správně zodpovězeno:</strong> <?php echo $_SESSION['correct_answers']; ?> /
                    <strong>Zbývá:</strong> <?php echo max(0, $totalQuestions - $_SESSION['correct_answers']); ?>
                </div>
            </div>

            <!-- Kompas -->
<div id="compass-container" style="text-align: center; margin: 40px auto; padding: 20px; border: 2px solid #ccc; border-radius: 10px; background: #f9f9f9; max-width: 200px;">
    <h3 style="margin-bottom: 10px; font-size: 1.2em; color: #333;">Kompas</h3>
    <div id="compass" style="position: relative; width: 150px; height: 150px; margin: 0 auto;">
        <img id="compass-image" src="assets/kompas.png" alt="Kompas" style="width: 100%; height: auto; border-radius: 50%; transform-origin: center; transform: rotate(0deg);">
    </div>
</div>
            <script>
                // Hamburger menu logika
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

                // Otevřené menu na desktopu, zavřené na mobilu
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

                    // Souřadnice otázky
    const questionLat = <?php echo floatval($questionRow['location_lat']); ?>;
    const questionLng = <?php echo floatval($questionRow['location_lng']); ?>;

    // Element obrázku kompasu
    const compassImage = document.getElementById('compass-image');

    // Funkce pro výpočet azimutu mezi dvěma body
    function calculateBearing(lat1, lng1, lat2, lng2) {
        const toRadians = (deg) => deg * (Math.PI / 180);
        const toDegrees = (rad) => rad * (180 / Math.PI);

        const dLng = toRadians(lng2 - lng1);
        const y = Math.sin(dLng) * Math.cos(toRadians(lat2));
        const x = Math.cos(toRadians(lat1)) * Math.sin(toRadians(lat2)) -
                  Math.sin(toRadians(lat1)) * Math.cos(toRadians(lat2)) * Math.cos(dLng);
        return (toDegrees(Math.atan2(y, x)) + 360) % 360;
    }

    // Funkce pro aktualizaci směru kompasu
    function updateCompass(userLat, userLng) {
        const bearing = calculateBearing(userLat, userLng, questionLat, questionLng);
        compassImage.style.transform = `rotate(${bearing}deg)`;
    }

    // Získání polohy uživatele
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(
            (position) => {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                updateCompass(userLat, userLng);
            },
            (error) => {
                console.error('Chyba geolokace:', error);
            },
            { enableHighAccuracy: true }
        );
    } else {
        alert('Geolokace není podporována vaším prohlížečem.');
    }

    // Funkce pro aktualizaci směru kompasu na základě orientace zařízení
    function updateCompassRotation(event) {
        const alpha = event.alpha; // Úhel rotace zařízení kolem jeho vertikální osy
        compassImage.style.transform = `rotate(${360 - alpha}deg)`;
    }

    // Kontrola podpory DeviceOrientationEvent
    if (window.DeviceOrientationEvent) {
        window.addEventListener('deviceorientation', updateCompassRotation, true);
    } else {
        alert('Vaše zařízení nepodporuje orientaci zařízení.');
    }
            </script>
        </body>
        </html>
        <?php
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'No question found']);
}

// Fetch all questions from the database
$questionsResult = $conn->query("SELECT id, question_text FROM questions");
?>
<div style="margin: 40px auto; max-width: 600px; text-align: center;">
    <h2>Výběr otázky</h2>
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Text otázky</th>
                <th>Akce</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($question = $questionsResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($question['id']); ?></td>
                    <td><?php echo htmlspecialchars($question['question_text']); ?></td>
                    <td>
                        <form method="post" style="margin: 0;">
                            <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                            <button type="submit">Vybrat</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php
$conn->close();
