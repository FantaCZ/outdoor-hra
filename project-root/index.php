<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hlavní stránka</title>
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/style.css"> <!-- Přidání odkazu na styl -->
</head>
<body class="index-page">


    <!-- Obsah stránky -->
    <nav class="index-nav">
        <ul>
            <li><a href="index.php" class="nav-link">Domů</a></li>
            <li><a href="game.php" class="nav-link">Hrát</a></li>
            <li><a href="login.php" class="nav-link">Přihlášení</a></li>
            <li><a href="register.php" class="nav-link">Registrace</a></li>
            <li><a href="rules.php" class="nav-link">Pravidla</a></li>
            <li><a href="about.php" class="nav-link">O nás</a></li>
        </ul>
    </nav>
    <h1>Vítejte v pražském escape roomu!</h1>
    <p>Pro více informací si můžete prohlédnout naše pravidla nebo se dozvědět více o nás.</p>

    <!-- Fotky -->
    <section class="photo-gallery">
        <h2>Jaké místa obsahuje tato hra?</h2>
        <div class="photos">
            <img src="assets/praha_main.png" alt="Photo 1" onclick="openModal(this)">
            <img src="assets/mala_strana.jpg" alt="Photo 2" onclick="openModal(this)">
            <img src="assets/letna.jpg" alt="Photo 3" onclick="openModal(this)">
        </div>
    </section>

    <!-- Modal pro zvětšený obrázek -->
    <div id="myModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-content">
            <img id="modalImage" src="">
        </div>
    </div>

    <script>
        function openModal(element) {
            document.getElementById('myModal').style.display = "block";
            document.getElementById('modalImage').src = element.src;
        }

        function closeModal() {
            document.getElementById('myModal').style.display = "none";
        }
    </script>
</body>
</html>
