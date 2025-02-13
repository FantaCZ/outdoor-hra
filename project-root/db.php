<?php
$host = "localhost";
$dbname = "escape_game";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

/* Zkontrolujeme připojení
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Připojení k databázi bylo úspěšné.";
} */
?>
