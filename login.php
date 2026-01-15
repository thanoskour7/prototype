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
    <link rel="stylesheet" href="style.css">
    <style>
        /* ======== LOGIN PAGE CUSTOM STYLE ======== */
        body {
            background: linear-gradient(135deg, #004080, #0066cc);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header, footer {
            background: transparent;
            box-shadow: none;
        }

        .login-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 400px;
            padding: 40px 30px;
            text-align: center;
            animation: fadeIn 0.8s ease;
        }

        .login-card h2 {
            color: #003366;
            margin-bottom: 20px;
        }

        .login-card form {
            display: flex;
            flex-direction: column;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 1em;
            transition: all 0.3s;
        }

        .input-group input:focus {
            border-color: #0066cc;
            box-shadow: 0 0 6px rgba(0,102,204,0.3);
            outline: none;
        }

        .input-group i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        button {
            padding: 12px;
            border: none;
            border-radius: 10px;
            background-color: #0059b3;
            color: #fff;
            font-weight: bold;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #003f80;
            transform: translateY(-2px);
        }

        .register-link {
            margin-top: 15px;
            font-size: 0.9em;
        }

        .register-link a {
            color: #004080;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        p.error {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <script src="https://kit.fontawesome.com/a2e0e6ad11.js" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <h1>University of Larissa</h1>
    </header>

    <div class="login-container">
        <div class="login-card">
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