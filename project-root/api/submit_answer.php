<?php
include "../db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Získání surového vstupu
$raw_input = file_get_contents("php://input");

// Ověření typu obsahu
$content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';

// Pokud je obsah JSON, zpracujeme ho
if (strpos($content_type, 'application/json') !== false) {
    $data = json_decode($raw_input, true);
    if ($data === null) {
        die(json_encode(['error' => 'Chyba při dekódování JSON']));
    }
    $question_id = isset($data["question_id"]) ? (int)$data["question_id"] : null;
    $answer = isset($data["answer"]) ? trim($data["answer"]) : null;
} else {
    die(json_encode(["error" => "Nepodporovaný typ obsahu"]));  // Vracení chyby ve formátu JSON
}

// Pokud chybí parametry
if ($question_id === null || $answer === null) {
    die(json_encode(['error' => 'Chybí parametry question_id nebo answer']));  // Vracení chyby ve formátu JSON
}

// Zpracování odpovědi
$answer = strtolower($answer);  // Proveď malé písmena pro zjednodušení porovnání

// SQL dotaz pro ověření odpovědi
$stmt = $conn->prepare("SELECT correct_answer FROM questions WHERE id = ?");
if ($stmt === false) {
    die(json_encode(['error' => 'Prepare failed: ' . $conn->error]));  // Vracení chyby ve formátu JSON
}

$stmt->bind_param("i", $question_id);
if (!$stmt->execute()) {
    die(json_encode(['error' => 'Execute failed: ' . $stmt->error]));  // Vracení chyby ve formátu JSON
}

/* $stmt->bind_result($correct_answer);
if ($stmt->fetch()) {
    // Porovnání odpovědi
    if (strtolower($correct_answer) === $answer) {
        echo json_encode(['status' => 'success', 'message' => 'Správná odpověď']);
    } else {
        echo json_encode(['status' => 'failure', 'message' => 'Špatná odpověď']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No answer found for question_id = ' . $question_id]);
}
 */
$stmt->close();
?>
