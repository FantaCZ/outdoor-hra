<?php
// Database connection (adjust credentials as needed)
$conn = new mysqli('localhost', 'root', '', 'escape_game');
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Fetch a random question with location
$sql = "SELECT id, question_text, location_lat, location_lng FROM questions ORDER BY RAND() LIMIT 1";
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {
    // Pokud je požadavek AJAX (např. fetch), vrať JSON
    if (
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        header('Content-Type: application/json');
        echo json_encode([
            'id' => $row['id'],
            'question' => $row['question_text'],
            // 'allowed_distance' => 50, // Zakomentováno dle požadavku
            'latitude' => floatval($row['location_lat']),
            'longitude' => floatval($row['location_lng'])
        ]);
    } else {
        // Jinak vypiš HTML
        ?>
        <!DOCTYPE html>
        <html lang="cs">
        <head>
            <meta charset="UTF-8">
            <title>Otázka</title>
            <style>
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
            </style>
        </head>
        <body>
            <div id="question-container">
                <div id="question-text"><?php echo htmlspecialchars($row['question_text']); ?></div>
                <form method="post">
                    <input type="hidden" name="question_id" value="<?php echo $row['id']; ?>">
                    <input type="text" id="answer-input" name="answer" placeholder="Zadejte odpověď">
                    <button id="answer-btn" type="submit">Potvrdit</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'No question found']);
}

$conn->close();