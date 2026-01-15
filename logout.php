<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Αποσύνδεση | University of Larissa</title>
    <!-- CSS Αρχεία - Φορτώνουμε τα styles σε σειρά -->
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/responsive.css">
    <script src="https://kit.fontawesome.com/a2e0e6ad11.js" crossorigin="anonymous"></script>
    <meta http-equiv="refresh" content="3;url=index.php">
</head>
<body>
    <header>
        <h1>University of Larissa</h1>
        <nav>
            <a href="index.php">Αρχική</a>
            <a href="login.php">Σύνδεση</a>
        </nav>
    </header>

    <div class="page-container">
        <div class="form-card" style="max-width: 500px;">
            <i class="fas fa-sign-out-alt"></i>
            <h2>Αποσυνδεθήκατε επιτυχώς</h2>
            <p>Η συνεδρία σας τερματίστηκε με επιτυχία.</p>
            <p>Θα επιστρέψετε αυτόματα στην αρχική σελίδα</p>
            <p><a href="index.php">Επιστροφή τώρα</a></p>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> University of Larissa</p>
    </footer>
</body>
</html>
