<?php
include "../db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Always return JSON

$raw_input = file_get_contents("php://input");
$content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';

if (strpos($content_type, 'application/json') !== false) {
    $data = json_decode($raw_input, true);
    if ($data === null) {
        echo json_encode(['status' => 'error', 'message' => 'Chyba při dekódování JSON']);
        exit;
    }
    $question_id = isset($data["question_id"]) ? (int)$data["question_id"] : null;
    $answer = isset($data["answer"]) ? trim($data["answer"]) : null;
} else {
    echo json_encode(["status" => "error", "message" => "Nepodporovaný typ obsahu"]);
    exit;
}

if ($question_id === null || $answer === null) {
    echo json_encode(['status' => 'error', 'message' => 'Chybí parametry question_id nebo answer']);
    exit;
}

$answer = strtolower($answer);

$stmt = $conn->prepare("SELECT correct_answer FROM questions WHERE id = ?");
if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $question_id);
if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error]);
    $stmt->close();
    exit;
}

$stmt->bind_result($correct_answer);
if ($stmt->fetch()) {
    if (strtolower($correct_answer) === $answer) {
        echo json_encode(['status' => 'success', 'message' => 'Správná odpověď']);
    } else {
        echo json_encode(['status' => 'failure', 'message' => 'Špatná odpověď']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Otázka nenalezena']);
}
$stmt->close();
?>
