// Funkce pro inicializaci mapy, která bude globálně dostupná
function initMap() {
    console.log("Funkce initMap byla zavolána.");

    // Vytvoření mapy
    const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 50.0903, lng: 14.4000 }, // Výchozí souřadnice pro mapu
        zoom: 14
    });

    console.log("Mapa byla vytvořena.");

    // Přidání markeru na souřadnice V Kněžívce 240, 252 67 Tuchoměřice
    const lat = 50.12998057029644;
    const lng = 14.274981526348776;

    const marker = new google.maps.Marker({
        position: { lat, lng },
        map: map,
        title: "V Kněžívce 240, 252 67 Tuchoměřice" // Titulek markeru
    });

    // Infowindow pro tento marker
    const infowindow = new google.maps.InfoWindow({
        content: "<p>V Kněžívce 240, 252 67 Tuchoměřice</p>"
    });

    marker.addListener('click', () => {
        infowindow.open(map, marker);
    });
}

fetch('api/submit_answer.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
        question_id: 1,  // Tohle nahraď skutečnými daty
        answer: "Moje odpověď",
        user_id: 123
    })
})
.then(response => response.json())
.then(data => console.log("Server odpověděl:", data))
.catch(error => console.error('Chyba:', error));

