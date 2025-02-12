let map;

function initMap() {
    console.log("Funkce initMap byla zavolána.");  // Přidejte log pro testování
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 50.0755, lng: 14.4378 }, // Praha
        zoom: 13
    });

    console.log("Mapa byla vytvořena.");

    fetch("api/fetch_questions.php")
        .then(response => response.json())
        .then(data => {
            console.log(data);  // Zobrazení dat z databáze
            data.forEach(question => {
                let marker = new google.maps.Marker({
                    position: { lat: parseFloat(question.location_lat), lng: parseFloat(question.location_lng) },
                    map: map,
                    title: "Otázka"
                });

                marker.addListener("click", () => {
                    alert(`Otázka: ${question.question_text}`);
                });
            });
        })
        .catch(error => {
            console.log("Chyba při načítání otázek:", error);
        });
}

// Spustí se po načtení stránky
window.onload = initMap;
