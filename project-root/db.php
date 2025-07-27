<?php
header('Content-Type: text/html; charset=utf-8');

$host = "md413.wedos.net";
$dbname = "d220410_franta";
$username = "w220410_franta";
$password = "3CRcTxVs";

$conn = new mysqli($host, $username, $password, $dbname);

// Nastavení kódování na UTF-8 pro podporu češtiny
$conn->set_charset("utf8mb4");


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Připojení k databázi bylo úspěšné.";
} 
?>