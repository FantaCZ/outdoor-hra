# Outdoor Hra - Klíčové kusy kódu pro prezentaci

**Projekt:** Únik z Pražské pasti - lokační úniková hra po Praze
**Stack:** PHP + MySQL (backend), Vanilla JS + Leaflet.js (frontend)
**Koncept:** Hráč fyzicky chodí po Praze, na mapě vidí otázky. Když se přiblíží na 20 metrů k místu, zobrazí se mu otázka. Odpovídá na kvízové otázky o pražských památkách.

---

## 1. Haversinův vzorec - výpočet vzdálenosti na Zemi

**Soubor:** `project-root/game1.php`, řádky 314-327

Tohle je matematické jádro celé hry. Haversinův vzorec počítá vzdálenost mezi dvěma GPS souřadnicemi po povrchu koule (Země). Využívá se ke zjištění, jestli je hráč dostatečně blízko k otázce.

```javascript
function calculateDistance(lat1, lng1, lat2, lng2) {
    const R = 6371e3; // Poloměr Země v metrech
    const φ1 = lat1 * Math.PI / 180;
    const φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2 - lat1) * Math.PI / 180;
    const Δλ = (lng2 - lng1) * Math.PI / 180;

    const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c; // Vzdálenost v metrech
}
```

**Proč je to zajímavé:** Není to jen jednoduchý Pythagorův vzorec - ten by na zakřivené Zemi dával nepřesné výsledky. Haversinův vzorec bere v úvahu zakřivení zeměkoule a dává přesnost na jednotky metrů.

---

## 2. Realtime sledování polohy + proximity trigger

**Soubor:** `project-root/game1.php`, řádky 344-387

Hra nepoužívá jednorázové `getCurrentPosition()`, ale kontinuální `watchPosition()` - prohlížeč neustále posílá aktualizace GPS polohy. Při každé aktualizaci se přepočítá vzdálenost ke všem otázkám a najde se ta nejbližší.

```javascript
navigator.geolocation.watchPosition(
    (position) => {
        userLat = position.coords.latitude;
        userLng = position.coords.longitude;

        // Aktualizace markeru hráče na mapě
        if (window.userMarker) {
            window.userMarker.setLatLng([userLat, userLng]);
        } else {
            window.userMarker = L.marker([userLat, userLng], { icon: blueIcon }).addTo(map);
        }

        // Zelený kruh 20m kolem hráče
        if (userCircle) {
            userCircle.setLatLng([userLat, userLng]);
        } else {
            userCircle = L.circle([userLat, userLng], {
                radius: 20,
                color: '#4CAF50',
                fillColor: '#4CAF50',
                fillOpacity: 0.2
            }).addTo(map);
        }

        // Najdi nejbližší otázku
        const { closestQuestion, minDistance } = findClosestQuestion(userLat, userLng);

        // Zobraz formulář jen když je hráč do 20 metrů
        if (closestQuestion && minDistance <= 20) {
            questionContainer.style.display = 'block';
            questionText.innerText = closestQuestion.question_text;
            questionIdInput.value = closestQuestion.id;
        } else {
            questionContainer.style.display = 'none';
        }
    },
    (error) => { console.error('Chyba geolokace:', error); },
    { enableHighAccuracy: true }
);
```

**Proč je to zajímavé:** Celá herní mechanika stojí na tomhle - hráč se fyzicky pohybuje a hra na to reaguje v reálném čase. Zelený kruh vizuálně ukazuje "dosah" hráče, a formulář s otázkou se dynamicky zobrazí/schová podle proximity.

---

## 3. Výpočet azimutu (směr kompasu k cíli)

**Soubor:** `project-root/game.php`, řádky 330-345

Kompas ukazuje hráči směr k další otázce. Azimut se počítá z GPS souřadnic hráče a cíle pomocí inverzního tangentu.

```javascript
function calculateBearing(lat1, lng1, lat2, lng2) {
    const toRadians = (deg) => deg * (Math.PI / 180);
    const toDegrees = (rad) => rad * (180 / Math.PI);

    const dLng = toRadians(lng2 - lng1);
    const y = Math.sin(dLng) * Math.cos(toRadians(lat2));
    const x = Math.cos(toRadians(lat1)) * Math.sin(toRadians(lat2)) -
              Math.sin(toRadians(lat1)) * Math.cos(toRadians(lat2)) * Math.cos(dLng);
    return (toDegrees(Math.atan2(y, x)) + 360) % 360; // 0-360 stupňů
}

function updateCompass(userLat, userLng) {
    const bearing = calculateBearing(userLat, userLng, questionLat, questionLng);
    compassImage.style.transform = `rotate(${bearing}deg)`;
}
```

**Proč je to zajímavé:** Kombinace dvou věcí - GPS azimut (kam se dívat) a Device Orientation API (kam se telefon fyzicky otáčí). Výsledek je kompas, který ukazuje směr k další otázce jako skutečný kompas.

---

## 4. Device Orientation API - fyzický kompas

**Soubor:** `project-root/game.php`, řádky 364-375

Prohlížeč umí číst data z gyroskopu telefonu. Tohle rotuje obrázek kompasu podle toho, jak hráč otáčí telefonem.

```javascript
function updateCompassRotation(event) {
    const alpha = event.alpha; // Úhel rotace zařízení kolem vertikální osy
    compassImage.style.transform = `rotate(${360 - alpha}deg)`;
}

if (window.DeviceOrientationEvent) {
    window.addEventListener('deviceorientation', updateCompassRotation, true);
}
```

**Proč je to zajímavé:** Používá se nativní API prohlížeče pro přístup k hardwarovým senzorům telefonu - bez žádné externí knihovny. Hráč vidí kompas, který se otáčí s jeho telefonem.

---

## 5. Dynamické přiřazení otázek - každý hráč má jiný set

**Soubor:** `project-root/game1.php`, řádky 153-210

Při prvním spuštění hry se hráči náhodně vybere 10 z 13 otázek. Výběr se uloží do databáze, takže každý hráč má unikátní sadu a při reloadu stránky se mu načte stejná sada.

```php
function getQuestionsForUser($conn, $userId) {
    $userHasProgress = hasUserProgress($conn, $userId);

    if ($userHasProgress) {
        // Načti uložené otázky hráče z DB
        $sql = "
            SELECT q.id, q.question_text, q.location_lat, q.location_lng,
                   q.correct_answer, q.image_path
            FROM users u
            JOIN progress_questions pq ON u.progress_id = pq.progress_id
            JOIN questions q ON pq.question_id = q.id
            WHERE u.id = ? AND u.progress_id IS NOT NULL
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        // ...
    } else {
        // První spuštění - vyber 10 náhodných otázek
        $sql = "SELECT ... FROM questions ORDER BY RAND() LIMIT 10";

        // Vytvoř záznam v tabulce progress
        $conn->query("INSERT INTO progress () VALUES ()");
        $progressId = $conn->insert_id;

        // Ulož přiřazení otázek
        foreach ($questions as $row) {
            $insertStmt->bind_param("ii", $progressId, $row['id']);
            $insertStmt->execute();
        }

        // Propoj uživatele s progressem
        $update = $conn->prepare("UPDATE users SET progress_id = ? WHERE id = ?");
        $update->bind_param("ii", $progressId, $userId);
        $update->execute();
    }
    return $questions;
}
```

**Proč je to zajímavé:** Tenhle systém zařizuje, že: (a) každý hráč může dostat jiný výběr otázek, (b) postup se ukládá a přežije reload, (c) používá se JOIN přes 3 tabulky (`users` -> `progress_questions` -> `questions`).

---

## 6. Interaktivní mapa s Leaflet.js + omezené hranice

**Soubor:** `project-root/game1.php`, řádky 263-293

Mapa je omezena na oblast Prahy - hráč ji nemůže odscrollovat jinam. Otázky jsou zobrazeny jako červené markery, hráč jako modrý.

```javascript
// Hranice mapy - jen Praha
const bounds = L.latLngBounds(
    [50.05, 14.35], // Jihozápadní roh
    [50.15, 14.45]  // Severovýchodní roh
);

const map = L.map('map-container', {
    maxBounds: bounds,
    maxBoundsViscosity: 1.0  // Tvrdý limit - mapa se "neprotáhne" za hranice
}).setView(defaultCenter, defaultZoom);

// Načtení otázek z PHP do JS
const questions = <?php echo json_encode($questions); ?>;

// Červené markery pro otázky
questions.forEach(question => {
    const marker = L.marker(
        [question.location_lat, question.location_lng],
        { icon: redIcon }
    ).addTo(map);
    marker.bindPopup(`<strong>Otázka:</strong> ${question.question_text}`);
});
```

**Proč je to zajímavé:** PHP data se přímo injectují do JavaScriptu pomocí `json_encode()`. `maxBoundsViscosity: 1.0` vytváří "tvrdý" limit - mapa se vůbec nehýbe za hranice Prahy, což hráči nedovolí se ztratit.

---

## 7. Časomíra s persistencí přes localStorage

**Soubor:** `project-root/game1.php`, řádky 422-481

Časomíra běží od začátku hry a přežije refresh stránky (čas se ukládá do localStorage). Navíc - hra nedovolí hráči zavřít stránku, dokud nezodpoví všechny otázky.

```javascript
// Načti čas z localStorage (přežije reload)
if (localStorage.getItem('game_timer')) {
    elapsedSeconds = parseInt(localStorage.getItem('game_timer'), 10) || 0;
}

function startTimer() {
    timerInterval = setInterval(() => {
        elapsedSeconds++;
        localStorage.setItem('game_timer', elapsedSeconds);
        updateTimerDisplay();
    }, 1000);
}

// Zabránění opuštění stránky před dokončením
window.addEventListener('beforeunload', function (e) {
    const correctAnswers = <?php echo (int)$_SESSION['correct_answers']; ?>;
    const totalQuestions = <?php echo (int)$totalQuestions; ?>;
    if (correctAnswers < totalQuestions) {
        e.preventDefault();
        e.returnValue = 'Hru nelze opustit, dokud nezodpovíte všechny otázky!';
    } else {
        sendTimeToServer();
        localStorage.removeItem('game_timer');
    }
});
```

**Proč je to zajímavé:** Kombinace `localStorage` (client-side persistence), `setInterval` (časování) a `beforeunload` eventu (ochrana před opuštěním). Timer je vizuálně červený v pravém horním rohu - vytváří psychologický tlak na hráče.

---

## 8. Ověření odpovědi - case-insensitive porovnání

**Soubor:** `project-root/game1.php`, řádky 72-103

Backend ověřuje odpověď hráče. Porovnání je case-insensitive a trimuje whitespace, aby drobné překlepy v kapitálkách nebyly problém.

```php
function handleAnswer($conn, $userId, $questionId, $answer) {
    $stmt = $conn->prepare("SELECT correct_answer FROM questions WHERE id = ?");
    $stmt->bind_param("i", $questionId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $correctAnswer = $row['correct_answer'];

        // Case-insensitive porovnání s trimem
        if (trim(strtolower($answer)) === trim(strtolower($correctAnswer))) {
            $pid = getUserProgressId($conn, $userId);
            markQuestionAsAnswered($conn, $pid, $questionId);

            // Kontrola zda jsou zodpovězeny všechny otázky
            $progressFinished = areAllQuestionsAnswered($conn, $pid);
        }
    }
}
```

**Proč je to zajímavé:** Používá prepared statements (`bind_param`) proti SQL injection. Systém odpovědí je provázaný s progress trackingem - každá správná odpověď se uloží a hra ví, kdy je hotová.

---

## Architektura databáze

```
users                    progress              questions
+----------+            +----------+           +---------------+
| id (PK)  |            | id (PK)  |           | id (PK)       |
| username |            +----------+           | question_text |
| password |                 |                 | location_lat  |
| progress_id (FK) ------+  |                 | location_lng  |
+----------+             |  |                  | correct_answer|
                         v  v                  | image_path    |
                   progress_questions          +---------------+
                   +------------------+              |
                   | progress_id (FK) |--------------+
                   | question_id (FK) |
                   | answered (0/1)   |
                   +------------------+
```

Tři hlavní vazby: Uživatel -> Progress -> Otázky. Každý hráč má svůj unikátní progress s vlastní sadou otázek.

---

## Souhrn použitých technologií a API

| Technologie | Použití v projektu |
|---|---|
| **Haversinův vzorec** | Výpočet GPS vzdálenosti |
| **Geolocation API** | `watchPosition()` pro real-time sledování |
| **Device Orientation API** | Fyzický kompas z gyroskopu |
| **Leaflet.js** | Interaktivní mapa s markery |
| **OpenStreetMap** | Mapové dlaždice |
| **localStorage** | Persistentní časomíra |
| **beforeunload event** | Ochrana před opuštěním hry |
| **PHP Sessions** | Stav přihlášení a skóre |
| **MySQL prepared statements** | Bezpečné DB dotazy |
| **json_encode (PHP -> JS)** | Předání dat ze serveru do klienta |
