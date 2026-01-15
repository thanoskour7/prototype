<?php
/*
 * Dashboard για Φοιτητές
 * Περιέχει όλες τις λειτουργίες: Μαθήματα, Βαθμολογίες
 */
session_start();
require 'db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'courses';

// ========== ΕΓΓΡΑΦΗ ΣΕ ΜΑΘΗΜΑ ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['enroll'])) {
    $course_id = $_POST['course_id'];
    $check = $conn->prepare("SELECT id FROM enrollments WHERE student_id=? AND course_id=?");
    $check->bind_param("ii", $student_id, $course_id);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $student_id, $course_id);
        $stmt->execute();
        $message = "Εγγραφήκατε επιτυχώς στο μάθημα!";
    } else {
        $error = "Είστε ήδη εγγεγραμμένος σε αυτό το μάθημα.";
    }
}

// ========== ΛΗΨΗ ΔΕΔΟΜΕΝΩΝ ==========
// Μαθήματα
$courses_query = "SELECT c.*, u.username as professor_name 
                  FROM courses c 
                  JOIN users u ON c.professor_id = u.id 
                  WHERE c.is_visible = 1 
                  ORDER BY c.title";
$courses_result = $conn->query($courses_query);

$enrolled_query = "SELECT course_id FROM enrollments WHERE student_id = ?";
$enrolled_stmt = $conn->prepare($enrolled_query);
$enrolled_stmt->bind_param("i", $student_id);
$enrolled_stmt->execute();
$enrolled_result = $enrolled_stmt->get_result();
$enrolled_courses = [];
while ($row = $enrolled_result->fetch_assoc()) {
    $enrolled_courses[] = $row['course_id'];
}

// Βαθμολογίες
$grades_query = "SELECT g.*, c.title as course_title
                 FROM grades g
                 JOIN courses c ON g.course_id = c.id
                 WHERE g.student_id = ?
                 ORDER BY g.created_at DESC";
$grades_stmt = $conn->prepare($grades_query);
$grades_stmt->bind_param("i", $student_id);
$grades_stmt->execute();
$grades_result = $grades_stmt->get_result();

$enrolled_no_grade_query = "SELECT c.* 
                            FROM courses c
                            JOIN enrollments e ON c.id = e.course_id
                            WHERE e.student_id = ?
                            AND c.id NOT IN (SELECT course_id FROM grades WHERE student_id = ?)
                            ORDER BY c.title";
$enrolled_no_grade_stmt = $conn->prepare($enrolled_no_grade_query);
$enrolled_no_grade_stmt->bind_param("ii", $student_id, $student_id);
$enrolled_no_grade_stmt->execute();
$enrolled_no_grade_result = $enrolled_no_grade_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Φοιτητής | University of Larissa</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        /* 
           Tabs για να αλλάζουμε μεταξύ των ενότητων
        */
        .tabs {
            display: flex;
            gap: 10px;
            margin: 30px 0;
            border-bottom: 2px solid #ddd;
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
        <p>Είστε συνδεδεμένος ως <strong>Φοιτητής</strong>.</p>
        
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
            <button class="tab-button <?php echo $active_tab == 'grades' ? 'active' : ''; ?>" 
                    onclick="showTab('grades')">Βαθμολογίες</button>
        </div>

        <!-- Tab: Μαθήματα -->
        <div id="tab-courses" class="tab-content <?php echo $active_tab == 'courses' ? 'active' : ''; ?>">
            <h3>Διαθέσιμα Μαθήματα</h3>
            <div class="card-grid">
                <?php if($courses_result->num_rows > 0): ?>
                    <?php while($course = $courses_result->fetch_assoc()): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                            <p><strong>Καθηγητής:</strong> <?php echo htmlspecialchars($course['professor_name']); ?></p>
                            <p><?php echo htmlspecialchars($course['description']); ?></p>
                            
                            <?php if(in_array($course['id'], $enrolled_courses)): ?>
                                <p style="color: green; font-weight: bold; margin-top: 10px;">✓ Είστε εγγεγραμμένος</p>
                            <?php else: ?>
                                <form method="POST" style="margin-top: 10px;">
                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                    <button type="submit" name="enroll">Εγγραφή στο Μάθημα</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Δεν υπάρχουν διαθέσιμα μαθήματα αυτή τη στιγμή.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tab: Βαθμολογίες -->
        <div id="tab-grades" class="tab-content <?php echo $active_tab == 'grades' ? 'active' : ''; ?>">
            <h3>Οι Βαθμολογίες μου</h3>
            
            <?php if($grades_result->num_rows > 0): ?>
                <h4 style="margin-top: 20px;">Μαθήματα με Βαθμολογίες</h4>
                <div class="card-grid">
                    <?php 
                    $grades_result->data_seek(0);
                    while($grade = $grades_result->fetch_assoc()): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($grade['course_title']); ?></h3>
                            <p style="font-size: 2em; font-weight: bold; color: #0059b3; margin: 15px 0;">
                                <?php echo $grade['grade'] !== null ? number_format($grade['grade'], 2) : 'Δεν έχει βαθμολογηθεί'; ?>
                            </p>
                            <?php if($grade['notes']): ?>
                                <p><strong>Σχόλια:</strong> <?php echo htmlspecialchars($grade['notes']); ?></p>
                            <?php endif; ?>
                            <p style="font-size: 0.9em; color: #666;">
                                <?php echo date('d/m/Y', strtotime($grade['created_at'])); ?>
                            </p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <?php if($enrolled_no_grade_result->num_rows > 0): ?>
                <h4 style="margin-top: 30px;">Μαθήματα σε Αναμονή Βαθμολογίας</h4>
                <div class="card-grid">
                    <?php while($course = $enrolled_no_grade_result->fetch_assoc()): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                            <p><?php echo htmlspecialchars($course['description']); ?></p>
                            <p style="color: #666; font-style: italic;">Δεν έχει βαθμολογηθεί ακόμα</p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <?php if($grades_result->num_rows == 0 && $enrolled_no_grade_result->num_rows == 0): ?>
                <p>Δεν έχετε εγγραφεί σε κανένα μάθημα ακόμα.</p>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> University of Larissa</p>
    </footer>

    <script>
        function showTab(tabName) {
            // Κρύβουμε όλα τα tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Αφαιρούμε το active από όλα τα buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Εμφανίζουμε το επιλεγμένο tab
            document.getElementById('tab-' + tabName).classList.add('active');
            
            // Προσθέτουμε active στο button
            event.target.classList.add('active');
            
            // Αλλάζουμε το URL χωρίς reload
            window.history.pushState({}, '', '?tab=' + tabName);
        }
    </script>
</body>
</html>
