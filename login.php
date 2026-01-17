<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role_id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $hashed_password, $role_id);

    if ($stmt->fetch()) {
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role_id'] = $role_id;

            if ($role_id == 1) {
                header("Location: student_dashboard.php");
            } else {
                header("Location: professor_dashboard.php");
            }
            exit();
        } else {
            $error = "Λάθος κωδικός πρόσβασης.";
        }
    } else {
        $error = "Ο χρήστης δεν βρέθηκε.";
    }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Σύνδεση | University of Larissa</title>
    <!-- CSS Αρχεία - Φορτώνουμε τα styles σε σειρά -->
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/responsive.css">
    <!-- Ειδικά styles για τη σελίδα login -->
    <style>
        /* 
           Στη σελίδα login θέλουμε διαφορετικό 
           background χρώμα για το body.
        */
        body {
            background: linear-gradient(135deg, #004080, #0066cc);
        }

        /* 
           Στη σελίδα login το header και footer 
           είναι διαφανή (transparent).
        */
        header, footer {
            background: transparent;
            box-shadow: none;
        }
    </style>
</head>
<body>
    <header>
        <h1>University of Larissa</h1>
    </header>

    <div class="page-container">
        <div class="form-card">
            <h2>Σύνδεση Χρήστη</h2>
            <form method="POST" action="">
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Κωδικός πρόσβασης" required>
                    <i class="fas fa-lock"></i>
                </div>
                <button type="submit">Σύνδεση</button>
            </form>
            <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
            <div class="register-link">
                Δεν έχετε λογαριασμό; <a href="register.php">Εγγραφείτε εδώ</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> University of Larissa</p>
    </footer>
</body>
</html>