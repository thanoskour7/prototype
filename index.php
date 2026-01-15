<?php
session_start();
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University of Larissa</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>
<body>
    <!--HEADER-->
    <header>
        <h1>University of Larissa</h1>
        <nav>
            <?php if(isset($_SESSION['username'])): ?>
                <span>Καλωσήρθες, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php">Αποσύνδεση</a>
            <?php else: ?>
                <a href="login.php">Σύνδεση</a>
                <a href="register.php">Εγγραφή</a>
            <?php endif; ?>
        </nav>
    </header>

    <!--BANNER-->
    <section id="banner">
        Welcome to the University of Larissa
    </section>

    <!--CAMPUS INFO-->
    <section id="info">
        <h2>Σχετικά με το Campus</h2>
        <p>
            Το <strong>University of Larissa</strong> βρίσκεται στην οδό <em>Παπαναστασίου 28, Λάρισα</em> (ΤΚ 41334)
            και προσφέρει υψηλού επιπέδου εκπαίδευση και ερευνητικές ευκαιρίες.
            Οι φοιτητές και οι καθηγητές απολαμβάνουν ένα σύγχρονο περιβάλλον μάθησης,
            με υποδομές που υποστηρίζουν την ακαδημαϊκή και προσωπική τους εξέλιξη.
        </p>
        <img src="pictures/campus.jpg" alt="Campus Image">
    </section>

    <!--MAP SECTION-->
    <section id="map">
        <h2>Τοποθεσία Campus</h2>
        <div id="campusMap"></div>
    </section>

    <!--FOOTER -->
    <footer>
        <p>&copy; <?php echo date("Y"); ?> University of Larissa — All Rights Reserved.</p>
    </footer>

    <!-- SCRIPTS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Leaflet Map Configuration
        var map = L.map('campusMap').setView([39.6370, 22.4201], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        L.marker([39.6370, 22.4201]).addTo(map)
            .bindPopup('<b>University of Larissa</b><br>Παπαναστασίου 28, Λάρισα')
            .openPopup();
    </script>
</body>
</html>
