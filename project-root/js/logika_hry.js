// Funkce pro inicializaci mapy, která bude globálně dostupná
function initMap() {
    console.log("Funkce initMap byla zavolána.");

    // Vytvoření mapy
    const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 50.0903, lng: 14.4000 }, // Výchozí souřadnice pro mapu
        zoom: 14
    });

    console.log("Mapa byla vytvořena.");


}

fetch('api/submit_answer.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },  // Ujistíme se, že je content type application/json
    body: JSON.stringify({
        question_id: 1,
        answer: "Moje odpověď",
        user_id: 123
    })
})
.then(response => response.json())  // Odpověď ve formátu JSON
.then(data => {
    console.log("Server odpověděl:", data);
    if (data.status === 'success') {
        alert('Správná odpověď');
    } else {
        alert('Špatná odpověď');
    }
})
.catch(error => console.error('Chyba:', error));




