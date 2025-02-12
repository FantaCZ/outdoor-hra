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

    // Načítání otázek z databáze
    fetch('/project-root/api/fetch_questions.php')
    .then(response => response.json())
    .then(questions => {
        questions.forEach(question => {
            // Zkontroluj, zda jsou souřadnice platné
            if (question.location_lat && question.location_lng) {
                const lat = parseFloat(question.location_lat);
                const lng = parseFloat(question.location_lng);

                // Zobrazení markeru pro každou otázku na mapě
                const marker = new google.maps.Marker({
                    position: { lat, lng },
                    map: map,
                    title: question.question_text // Text, který se zobrazí při najetí na marker
                });

                // Infowindow pro každý marker
                const infowindow = new google.maps.InfoWindow({
                    content: `<p>${question.question_text}</p>`
                });

                marker.addListener('click', () => {
                    infowindow.open(map, marker);
                });
            }
        });
    })
    .catch(error => {
        console.error("Chyba při načítání otázek:", error);
    });
}
