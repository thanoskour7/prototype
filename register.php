<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $code = $_POST['code'];

    if (($role == 'student' && $code != 'STUD2025') || ($role == 'professor' && $code != 'PROF2025')) {
        $error = "Λάθος ειδικός κωδικός.";
    } else {
        $role_id = ($role == 'student') ? 1 : 2;
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $username, $email, $hashed_password, $role_id);
        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Σφάλμα κατά την εγγραφή. Το email πιθανώς υπάρχει ήδη.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Εγγραφή | University of Larissa</title>
    <!-- CSS Αρχεία - Φορτώνουμε τα styles σε σειρά -->
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/responsive.css">
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
        <div class="form-card">
            <h2>Εγγραφή Νέου Χρήστη</h2>
            <form method="POST" action="">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Όνομα χρήστη" required>
                    <i class="fas fa-user"></i>
                </div>

                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class="fas fa-envelope"></i>
                </div>

                <div class="input-group">
                    <input type="password" name="password" placeholder="Κωδικός πρόσβασης" required>
                    <i class="fas fa-lock"></i>
                </div>

                <div class="input-group">
                    <select name="role" required>
                        <option value="">-- Επιλέξτε ρόλο --</option>
                        <option value="student">Φοιτητής</option>
                        <option value="professor">Καθηγητής</option>
                    </select>
                    <i class="fas fa-user-graduate"></i>
                </div>

                <div class="input-group">
                    <input type="text" name="code" placeholder="Ειδικός κωδικός (STUD2025 / PROF2025)" required>
                    <i class="fas fa-key"></i>
                </div>

                <button type="submit">Εγγραφή</button>
            </form>

            <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

            <div class="register-link">
                Έχετε ήδη λογαριασμό; <a href="login.php">Συνδεθείτε εδώ</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> University of Larissa</p>
    </footer>
</body>
</html>
