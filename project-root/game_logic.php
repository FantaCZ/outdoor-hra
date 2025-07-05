<?php

$mysqli = new mysqli("localhost", "root", "", "escape_game");
if ($mysqli->connect_errno) {
    http_response_code(500);
    exit("Database connection failed.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['username'], $_POST['answered_correctly'], $_POST['completion_time'])) {
    $user_id = intval($_POST['user_id']);
    $username = $mysqli->real_escape_string($_POST['username']);
    $answered_correctly = intval($_POST['answered_correctly']);
    $completion_time = $mysqli->real_escape_string($_POST['completion_time']); // očekává se formát HH:MM:SS

    $stmt = $mysqli->prepare("INSERT INTO user_progress (user_id, username, answered_correctly, completion_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $user_id, $username, $answered_correctly, $completion_time);

    if ($stmt->execute()) {
        echo "Progress saved.";
    } else {
        http_response_code(500);
        echo "Error saving progress.";
    }
    $stmt->close();
    $mysqli->close();
    exit;
}

// ...existing code...
?>