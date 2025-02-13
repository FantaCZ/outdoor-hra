<?php
include('../db.php');  // Pokud je db.php v kořenovém adresáři

// SQL dotaz na výběr všech otázek z databáze
$sql = "SELECT * FROM questions";
$result = $conn->query($sql);

$questions = array();

if ($result->num_rows > 0) {
    // Načtení všech výsledků z databáze
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
} else {
    // Pokud nejsou žádné výsledky, vrátí chybovou zprávu
    echo json_encode(["error" => "0 results"]);
}

// Vrátí otázky ve formátu JSON
echo json_encode($questions);
?>
