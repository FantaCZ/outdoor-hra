<?php
include "../db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Zkusíme zjistit, co přesně přichází
echo "RAW INPUT:\n";
echo file_get_contents("php://input");





$question_id = isset($_POST["question_id"]) ? (int)$_POST["question_id"] : null;
$answer = isset($_POST["answer"]) ? trim($_POST["answer"]) : null;

if ($question_id === null || $answer === null) {
    die('Chybí parametry question_id nebo answer.');
}

$answer = strtolower($answer);  // Pokud je $answer prázdný, nic se nezkazí

$stmt = $conn->prepare("SELECT correct_answer FROM questions WHERE id = ?");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param("i", $question_id);
if (!$stmt->execute()) {
    die('Execute failed: ' . $stmt->error);
}

$stmt->bind_result($correct_answer);
if ($stmt->fetch()) {
    echo "Correct answer: " . $correct_answer;
} else {
    echo "No answer found for question_id = $question_id";
}

$stmt->close();

?>
