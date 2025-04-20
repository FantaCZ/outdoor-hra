// Funkce pro inicializaci mapy, která bude globálně dostupná
if (typeof map === 'undefined') {
    var map; // Definuj mapu globálně
}

function initMap() {
    console.log("Inicializace mapy...");
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 50.0903, lng: 14.4000 }, // Výchozí souřadnice pro mapu
        zoom: 17
    });

    console.log("Mapa byla vytvořena:", map);
}

function addMarker(lat, lng, questionText) {
    if (!map) {
        console.error("Chyba: Mapa není inicializovaná!");
        return;
    }

    const marker = new google.maps.Marker({
        position: { lat: lat, lng: lng },
        map: map,
        title: questionText
    });

    console.log("Marker přidán:", marker);
}

function centerMapOnPlayer(playerLocation, zoomLevel = 17) {
    if (!map) {
        console.error("Chyba: Mapa není inicializovaná!");
        return;
    }

    map.setCenter(playerLocation);
    map.setZoom(zoomLevel);
    console.log("Mapa vycentrována na polohu hráče a přiblížena na úroveň:", zoomLevel);
}

// Žádost o GPS polohu hráče
if (navigator.geolocation) {
    navigator.geolocation.watchPosition(position => {
        const playerLocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
        };

        // Marker pro hráče
        const playerMarker = new google.maps.Marker({
            position: playerLocation,
            map: map,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 7,
                fillColor: "blue",
                fillOpacity: 1,
                strokeWeight: 2,
                strokeColor: "white"
            }
        });

        // Kruh o poloměru 50 metrů kolem hráče
        const radiusCircle = new google.maps.Circle({
            center: playerLocation,
            radius: 50, // Poloměr v metrech
            map: map,
            fillColor: "#4b0082",
            fillOpacity: 0.2,
            strokeWeight: 1,
            strokeColor: "#4b0082"
        });

        // Aktualizace mapy při pohybu hráče
        centerMapOnPlayer(playerLocation, 17); // Přiblížení na úroveň 17
        playerMarker.setPosition(playerLocation);
        radiusCircle.setCenter(playerLocation);

        // Nastavení intervalu pro aktualizaci přiblížení mapy každých 30 sekund
        setInterval(() => {
            centerMapOnPlayer(playerLocation, 17); // Přiblížení na úroveň 17
        }, 30000); // 30000 ms = 30 sekund

    }, error => {
        console.error("Chyba při získávání GPS souřadnic:", error);
    }, { enableHighAccuracy: true });
} else {
    console.error("Geolokace není podporována.");
}

// Načtení otázek z API
fetch('http://localhost/outdoor-hra/project-root/api/fetch_questions.php')
    .then(response => response.json())
    .then(data => {
        data.forEach(question => {
            const marker = new google.maps.Marker({
                position: {
                    lat: parseFloat(question.location_lat),
                    lng: parseFloat(question.location_lng)
                },
                map: map,
                title: `Otázka #${question.id}`
            });

            // InfoWindow s ID otázky
            const infoWindow = new google.maps.InfoWindow({
                content: `<div><strong>Otázka ID:</strong> ${question.id}</div>`
            });

            marker.addListener("click", () => {
                infoWindow.open(map, marker);
            });
        });
    })
    .catch(error => console.error("Chyba při načítání otázek:", error));

// Odeslání odpovědi na otázku
fetch('http://localhost/outdoor-hra/project-root/api/submit_answer.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
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