<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Φοιτητής | University of Larissa</title>
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
            Είστε συνδεδεμένος ως <strong>Φοιτητής</strong>.  
            Από αυτή τη σελίδα θα έχετε πρόσβαση στις υπηρεσίες που αφορούν τους φοιτητές του Πανεπιστημίου Λάρισας.
        </p>

        <div class="card-grid">
            <div class="card">
                <h3>Προφίλ</h3>
                <p>Δείτε και επεξεργαστείτε τα προσωπικά σας στοιχεία.</p>
            </div>
            <div class="card">
                <h3>Μαθήματα</h3>
                <p>Δείτε τα διαθέσιμα μαθήματα και τις ανακοινώσεις.</p>
            </div>
            <div class="card">
                <h3>Βαθμολογίες</h3>
                <p>Πρόσβαση στις βαθμολογίες των μαθημάτων σας.</p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> University of Larissa</p>
    </footer>
</body>
</html>
