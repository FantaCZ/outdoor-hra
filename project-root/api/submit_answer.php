<?php
include "../db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Zkusíme zjistit, co přesně přichází
echo "RAW INPUT:\n";
echo file_get_contents("php://input");
exit;



$question_id = $_POST["question_id"];
$answer = trim($_POST["answer"]);

$stmt = $conn->prepare("SELECT correct_answer FROM questions WHERE id = ?");
$stmt->bind_param("i", $question_id);
$stmt->execute();
$stmt->bind_result($correct_answer);
$stmt->fetch();

if (strtolower($answer) === strtolower($correct_answer)) {
    echo "correct";
} else {
    echo "incorrect";
}
?>
