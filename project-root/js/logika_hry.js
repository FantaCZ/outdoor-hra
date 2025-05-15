// Helper: Calculate distance between two lat/lng points (Haversine formula)
function getDistanceFromLatLonInMeters(lat1, lon1, lat2, lon2) {
    const R = 6371000; // Radius of the earth in meters
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// Fetch question and image from backend
fetch('game.php')
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }
        // Display image and question
        document.getElementById('question-img').src = data.image_url;
        document.getElementById('question-text').textContent = data.question;

        // Disable answer input initially
        const answerInput = document.getElementById('answer-input');
        const answerBtn = document.getElementById('answer-btn');
        answerInput.disabled = true;
        answerBtn.disabled = true;

        // Get user's location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const userLat = position.coords.latitude;
                const userLon = position.coords.longitude;
                const dist = getDistanceFromLatLonInMeters(
                    userLat, userLon,
                    data.latitude, data.longitude
                );
                if (dist <= data.allowed_distance) {
                    answerInput.disabled = false;
                    answerBtn.disabled = false;
                    document.getElementById('distance-info').textContent = "Jste na správném místě!";
                } else {
                    document.getElementById('distance-info').textContent =
                        `Musíte být blíže (${Math.round(dist)} m, povoleno ${data.allowed_distance} m)`;
                }
            }, function() {
                document.getElementById('distance-info').textContent = "Nelze získat polohu.";
            });
        } else {
            document.getElementById('distance-info').textContent = "Geolokace není podporována.";
        }
    });