<?php
/*
 * Dashboard για Καθηγητές
 * Περιέχει όλες τις λειτουργίες: Μαθήματα, Φοιτητές, Ανακοινώσεις
 */
session_start();
require 'db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2){
    header("Location: login.php");
    exit();
}

$professor_id = $_SESSION['user_id'];
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'courses';

// ========== ΕΠΕΞΕΡΓΑΣΙΑ ΜΑΘΗΜΑΤΟΣ ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_course'])) {
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;
    
    // Ο καθηγητής μπορεί να ενημερώσει οποιοδήποτε μάθημα (όχι μόνο αυτά που έχει δημιουργήσει)
    $stmt = $conn->prepare("UPDATE courses SET title=?, description=?, is_visible=? WHERE id=?");
    $stmt->bind_param("ssii", $title, $description, $is_visible, $course_id);
    if($stmt->execute()) {
        $message = "Το μάθημα ενημερώθηκε επιτυχώς!";
    } else {
        $error = "Σφάλμα κατά την ενημέρωση του μαθήματος.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_course'])) {
    $course_id = $_POST['course_id'];
    // Ο καθηγητής μπορεί να διαγράψει οποιοδήποτε μάθημα
    $stmt = $conn->prepare("DELETE FROM courses WHERE id=?");
    $stmt->bind_param("i", $course_id);
    if($stmt->execute()) {
        $message = "Το μάθημα διαγράφηκε επιτυχώς!";
    } else {
        $error = "Σφάλμα κατά τη διαγραφή του μαθήματος.";
    }
}

// ========== ΒΑΘΜΟΛΟΓΙΑ ΦΟΙΤΗΤΩΝ ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_grade'])) {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $grade = $_POST['grade'];
    $notes = $_POST['notes'];
    
    $check = $conn->prepare("SELECT id FROM grades WHERE student_id=? AND course_id=?");
    $check->bind_param("ii", $student_id, $course_id);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE grades SET grade=?, notes=? WHERE student_id=? AND course_id=?");
        $stmt->bind_param("dsii", $grade, $notes, $student_id, $course_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO grades (student_id, course_id, grade, notes) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iids", $student_id, $course_id, $grade, $notes);
    }
    
    if($stmt->execute()) {
        $message = "Η βαθμολογία καταχωρήθηκε επιτυχώς!";
    } else {
        $error = "Σφάλμα κατά την καταχώρηση της βαθμολογίας.";
    }
}

// ========== ΑΝΑΚΟΙΝΩΣΕΙΣ ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_announcement'])) {
    $course_id = $_POST['course_id'] ?: null;
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    $stmt = $conn->prepare("INSERT INTO announcements (course_id, professor_id, title, content) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $course_id, $professor_id, $title, $content);
    if($stmt->execute()) {
        $message = "Η ανακοίνωση δημιουργήθηκε επιτυχώς!";
    } else {
        $error = "Σφάλμα κατά τη δημιουργία της ανακοίνωσης.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_announcement'])) {
    $announcement_id = $_POST['announcement_id'];
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id=? AND professor_id=?");
    $stmt->bind_param("ii", $announcement_id, $professor_id);
    if($stmt->execute()) {
        $message = "Η ανακοίνωση διαγράφηκε επιτυχώς!";
    } else {
        $error = "Σφάλμα κατά τη διαγραφή της ανακοίνωσης.";
    }
}

// ========== ΛΗΨΗ ΔΕΔΟΜΕΝΩΝ ==========
// Μαθήματα - Ο καθηγητής βλέπει ΟΛΑ τα courses (όχι μόνο αυτά που έχει δημιουργήσει)
// για να μπορεί να τα διαχειριστεί και να δει τους φοιτητές
$courses_query = "SELECT c.*, u.username as professor_name 
                  FROM courses c 
                  LEFT JOIN users u ON c.professor_id = u.id 
                  ORDER BY c.created_at DESC";
$courses_result = $conn->query($courses_query);

// Μαθήματα για dropdown - Όλα τα courses για να μπορεί να επιλέξει
$courses_dropdown_query = "SELECT * FROM courses ORDER BY title";
$courses_dropdown_result = $conn->query($courses_dropdown_query);

// Φοιτητές (για επιλεγμένο μάθημα)
$selected_course = isset($_GET['course_id']) ? $_GET['course_id'] : null;
$students_result = null;
if ($selected_course) {
    $students_query = "SELECT u.id, u.username, u.email, e.enrolled_at, g.grade, g.notes
                       FROM enrollments e
                       JOIN users u ON e.student_id = u.id
                       LEFT JOIN grades g ON u.id = g.student_id AND e.course_id = g.course_id
                       WHERE e.course_id = ?
                       ORDER BY u.username";
    $students_stmt = $conn->prepare($students_query);
    $students_stmt->bind_param("i", $selected_course);
    $students_stmt->execute();
    $students_result = $students_stmt->get_result();
}

// Ανακοινώσεις
$announcements_query = "SELECT a.*, c.title as course_title
                        FROM announcements a
                        LEFT JOIN courses c ON a.course_id = c.id
                        WHERE a.professor_id = ?
                        ORDER BY a.created_at DESC";
$announcements_stmt = $conn->prepare($announcements_query);
$announcements_stmt->bind_param("i", $professor_id);
$announcements_stmt->execute();
$announcements_result = $announcements_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Καθηγητής | University of Larissa</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        .tabs {
            display: flex;
            gap: 10px;
            margin: 30px 0;
            border-bottom: 2px solid #ddd;
            flex-wrap: wrap;
        }
        
        .tab-button {
            padding: 12px 24px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 1em;
            color: #666;
            font-weight: 500;
        }
        
        .tab-button:hover {
            color: #0059b3;
        }
        
        .tab-button.active {
            color: #0059b3;
            border-bottom-color: #0059b3;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
    </style>
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
        <p>Είστε συνδεδεμένος ως <strong>Καθηγητής</strong>.</p>
        
        <?php if(isset($message)): ?>
            <p style="color: green; font-weight: bold; margin-top: 15px;"><?php echo $message; ?></p>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <p style="color: red; font-weight: bold; margin-top: 15px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-button <?php echo $active_tab == 'courses' ? 'active' : ''; ?>" 
                    onclick="showTab('courses')">Μαθήματα</button>
            <button class="tab-button <?php echo $active_tab == 'students' ? 'active' : ''; ?>" 
                    onclick="showTab('students')">Φοιτητές</button>
            <button class="tab-button <?php echo $active_tab == 'announcements' ? 'active' : ''; ?>" 
                    onclick="showTab('announcements')">Ανακοινώσεις</button>
        </div>

        <!-- Tab: Μαθήματα -->
        <div id="tab-courses" class="tab-content <?php echo $active_tab == 'courses' ? 'active' : ''; ?>">
            <h3>Τα Μαθήματά μου</h3>
            <div class="card-grid">
                <?php if($courses_result->num_rows > 0): ?>
                    <?php 
                    $courses_result->data_seek(0);
                    while($course = $courses_result->fetch_assoc()): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                            <?php if(isset($course['professor_name'])): ?>
                                <p><strong>Καθηγητής:</strong> <?php echo htmlspecialchars($course['professor_name']); ?></p>
                            <?php endif; ?>
                            <p><?php echo htmlspecialchars($course['description']); ?></p>
                            <p><strong>Κατάσταση:</strong> 
                                <?php echo $course['is_visible'] ? '<span style="color: green;">Ορατό</span>' : '<span style="color: red;">Κρυφό</span>'; ?>
                            </p>
                            
                            <details style="margin-top: 15px;">
                                <summary style="cursor: pointer; color: #0059b3; font-weight: bold;">Επεξεργασία</summary>
                                <form method="POST" style="margin-top: 15px;">
                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                    <div class="input-group">
                                        <input type="text" name="title" value="<?php echo htmlspecialchars($course['title']); ?>" required>
                                    </div>
                                    <div class="input-group">
                                        <textarea name="description" rows="3" 
                                                  style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ccc; font-size: 1em; font-family: inherit;" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                                    </div>
                                    <div class="input-group">
                                        <label style="display: flex; align-items: center; gap: 10px;">
                                            <input type="checkbox" name="is_visible" <?php echo $course['is_visible'] ? 'checked' : ''; ?>>
                                            <span>Ορατό στους φοιτητές</span>
                                        </label>
                                    </div>
                                    <button type="submit" name="update_course">Ενημέρωση</button>
                                </form>
                                
                                <form method="POST" style="margin-top: 10px;" onsubmit="return confirm('Είστε σίγουρος ότι θέλετε να διαγράψετε αυτό το μάθημα;');">
                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                    <button type="submit" name="delete_course" style="background-color: #dc3545;">Διαγραφή</button>
                                </form>
                            </details>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Δεν υπάρχουν μαθήματα που να σας έχουν ανατεθεί.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tab: Φοιτητές -->
        <div id="tab-students" class="tab-content <?php echo $active_tab == 'students' ? 'active' : ''; ?>">
            <h3>Διαχείριση Φοιτητών</h3>
            
            <div class="form-card" style="max-width: 500px; margin: 20px auto;">
                <h4>Επιλέξτε Μάθημα</h4>
                <form method="GET">
                    <input type="hidden" name="tab" value="students">
                    <div class="input-group">
                        <select name="course_id" onchange="this.form.submit()" 
                                style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ccc; font-size: 1em;">
                            <option value="">-- Επιλέξτε μάθημα --</option>
                            <?php 
                            $courses_dropdown_result->data_seek(0);
                            while($course = $courses_dropdown_result->fetch_assoc()): ?>
                                <option value="<?php echo $course['id']; ?>" <?php echo $selected_course == $course['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </form>
            </div>

            <?php if($selected_course && $students_result && $students_result->num_rows > 0): ?>
                <h4 style="margin-top: 30px;">Εγγεγραμμένοι Φοιτητές</h4>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 10px; overflow: hidden;">
                        <thead>
                            <tr style="background-color: #003366; color: white;">
                                <th style="padding: 15px; text-align: left;">Όνομα Χρήστη</th>
                                <th style="padding: 15px; text-align: left;">Email</th>
                                <th style="padding: 15px; text-align: left;">Ημερομηνία Εγγραφής</th>
                                <th style="padding: 15px; text-align: left;">Βαθμός</th>
                                <th style="padding: 15px; text-align: left;">Ενέργειες</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $students_result->data_seek(0);
                            while($student = $students_result->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td style="padding: 15px;"><?php echo htmlspecialchars($student['username']); ?></td>
                                    <td style="padding: 15px;"><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td style="padding: 15px;"><?php echo date('d/m/Y', strtotime($student['enrolled_at'])); ?></td>
                                    <td style="padding: 15px;">
                                        <?php echo $student['grade'] !== null ? number_format($student['grade'], 2) : '-'; ?>
                                    </td>
                                    <td style="padding: 15px;">
                                        <details>
                                            <summary style="cursor: pointer; color: #0059b3; font-weight: bold;">Βαθμολόγηση</summary>
                                            <form method="POST" style="margin-top: 10px; padding: 15px; background: #f8faff; border-radius: 5px;">
                                                <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                                <input type="hidden" name="course_id" value="<?php echo $selected_course; ?>">
                                                <div class="input-group">
                                                    <input type="number" name="grade" step="0.01" min="0" max="10" 
                                                           placeholder="Βαθμός (0-10)" 
                                                           value="<?php echo $student['grade'] !== null ? $student['grade'] : ''; ?>" 
                                                           required>
                                                </div>
                                                <div class="input-group">
                                                    <textarea name="notes" placeholder="Σχόλια" rows="2" 
                                                              style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ccc; font-size: 1em; font-family: inherit;"><?php echo htmlspecialchars($student['notes'] ?? ''); ?></textarea>
                                                </div>
                                                <button type="submit" name="submit_grade">Αποθήκευση Βαθμολογίας</button>
                                            </form>
                                        </details>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif($selected_course): ?>
                <p style="margin-top: 20px;">Δεν υπάρχουν εγγεγραμμένοι φοιτητές σε αυτό το μάθημα.</p>
            <?php endif; ?>
        </div>

        <!-- Tab: Ανακοινώσεις -->
        <div id="tab-announcements" class="tab-content <?php echo $active_tab == 'announcements' ? 'active' : ''; ?>">
            <h3>Διαχείριση Ανακοινώσεων</h3>
            
            <div class="form-card" style="max-width: 600px; margin: 20px auto;">
                <h4>Δημιουργία Νέας Ανακοίνωσης</h4>
                <form method="POST">
                    <div class="input-group">
                        <select name="course_id" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ccc; font-size: 1em;">
                            <option value="">-- Γενική ανακοίνωση (όχι για συγκεκριμένο μάθημα) --</option>
                            <?php 
                            // Reset pointer για το dropdown των ανακοινώσεων
                            if($courses_dropdown_result->num_rows > 0) {
                                $courses_dropdown_result->data_seek(0);
                                while($course = $courses_dropdown_result->fetch_assoc()): ?>
                                    <option value="<?php echo $course['id']; ?>">
                                        <?php echo htmlspecialchars($course['title']); ?>
                                    </option>
                                <?php endwhile;
                            } ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <input type="text" name="title" placeholder="Τίτλος Ανακοίνωσης" required>
                    </div>
                    <div class="input-group">
                        <textarea name="content" placeholder="Περιεχόμενο Ανακοίνωσης" rows="6" 
                                  style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ccc; font-size: 1em; font-family: inherit;" required></textarea>
                    </div>
                    <button type="submit" name="create_announcement">Δημιουργία Ανακοίνωσης</button>
                </form>
            </div>

            <h4 style="margin-top: 40px;">Οι Ανακοινώσεις μου</h4>
            <div class="card-grid">
                <?php if($announcements_result->num_rows > 0): ?>
                    <?php while($announcement = $announcements_result->fetch_assoc()): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                            <?php if($announcement['course_title']): ?>
                                <p><strong>Μάθημα:</strong> <?php echo htmlspecialchars($announcement['course_title']); ?></p>
                            <?php else: ?>
                                <p><strong>Τύπος:</strong> Γενική ανακοίνωση</p>
                            <?php endif; ?>
                            <p><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                            <p style="font-size: 0.9em; color: #666; margin-top: 15px;">
                                <?php echo date('d/m/Y H:i', strtotime($announcement['created_at'])); ?>
                            </p>
                            
                            <form method="POST" style="margin-top: 15px;" onsubmit="return confirm('Είστε σίγουρος ότι θέλετε να διαγράψετε αυτή την ανακοίνωση;');">
                                <input type="hidden" name="announcement_id" value="<?php echo $announcement['id']; ?>">
                                <button type="submit" name="delete_announcement" style="background-color: #dc3545;">Διαγραφή</button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Δεν έχετε δημιουργήσει ανακοινώσεις ακόμα.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> University of Larissa</p>
    </footer>

    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById('tab-' + tabName).classList.add('active');
            event.target.classList.add('active');
            
            window.history.pushState({}, '', '?tab=' + tabName);
        }
    </script>
</body>
</html>
