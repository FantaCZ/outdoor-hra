<?php
include('../db.php');  // Pokud je db.php v kořenovém adresáři

// SQL dotaz na výběr všech otázek z databáze
$sql = "SELECT id, question_text FROM questions";
$result = $conn->query($sql);

$question_text = array();

$question_text = [];  // Inicializace prázdného pole pro otázky

if ($result->num_rows > 0) {
    // Načtení všech výsledků z databáze
    while ($row = $result->fetch_assoc()) {
        $questions = [
            ["id" => 1, "question_text" => "Otázka 1", "lat" => 50.0755, "lng" => 14.4378],
            ["id" => 2, "question_text" => "Otázka 2", "lat" => 50.0875, "lng" => 14.4208],
            ["id" => 3, "question_text" => "Otázka 3", "lat" => 50.0925, "lng" => 14.4388]
        ];
    }
    // Vrátí otázky ve formátu JSON
    echo json_encode($questions);
} else {
    // Pokud nejsou žádné výsledky, vrátí chybovou zprávu
    echo json_encode(["error" => "Žádné otázky v databázi"]);
}

$conn->close();  // Zavření připojení k databázi
?>
