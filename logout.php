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
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="refresh" content="3;url=index.php">
    <script src="https://kit.fontawesome.com/a2e0e6ad11.js" crossorigin="anonymous"></script>
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
            <i class="fas fa-sign-out-alt" style="font-size: 50px; color: #0059b3; margin-bottom: 15px;"></i>
            <h2>Αποσυνδεθήκατε επιτυχώς</h2>
            <p>Η συνεδρία σας τερματίστηκε με επιτυχία.</p>
            <p>Θα επιστρέψετε αυτόματα στην αρχική σελίδα</p>
            <p><a href="index.php" style="color:#004080; font-weight:bold;">Επιστροφή τώρα</a></p>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> University of Larissa</p>
    </footer>
</body>
</html>
