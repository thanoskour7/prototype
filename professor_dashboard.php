<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2){
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Καθηγητής | University of Larissa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>University of Larissa</h1>
        <nav>
            <a href="index.php">Αρχική</a>
            <a href="logout.php">Αποσύνδεση</a>
        </nav>
    </header>

    <section>
        <h2>Καλωσήρθες, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>
            Είστε συνδεδεμένος ως <strong>Καθηγητής</strong>.  
            Από εδώ μπορείτε να διαχειριστείτε τα μαθήματά σας, να δείτε τις εγγραφές φοιτητών και να ανεβάσετε ανακοινώσεις.
        </p>

        <div class="card-grid">
            <div class="card">
                <h3>Διαχείριση Μαθημάτων</h3>
                <p>Δημιουργήστε ή επεξεργαστείτε μαθήματα.</p>
            </div>
            <div class="card">
                <h3>Φοιτητές</h3>
                <p>Δείτε λίστες φοιτητών και διαχειριστείτε τις εγγραφές.</p>
            </div>
            <div class="card">
                <h3>Ανακοινώσεις</h3>
                <p>Αναρτήστε ανακοινώσεις και υλικό μαθήματος.</p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> University of Larissa</p>
    </footer>
</body>
</html>
