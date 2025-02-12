<!-- Načítani otazek z databaze
 -->
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
    echo "0 results"; // Pokud nejsou žádné výsledky
}

// Vrátí otázky ve formátu JSON
echo json_encode($questions);
?>
