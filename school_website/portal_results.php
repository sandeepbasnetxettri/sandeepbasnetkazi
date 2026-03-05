<?php
session_start();
if(!isset($_SESSION['user_logged_in']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit;
}
require_once 'config/db.php';

$user_id = $_SESSION['user_id'];

// Fetch student details
$stmt = $pdo->prepare("SELECT s.*, c.class_name FROM students s JOIN classes c ON s.class_id = c.id WHERE s.user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch();

if (!$student) {
    echo "Student profile not found.";
    exit;
}

$student_id = $student['id'];
$exam_term = $_GET['term'] ?? 'Final Term';

// Fetch Results
$stmt = $pdo->prepare("SELECT r.*, sub.subject_name FROM results r JOIN subjects sub ON r.subject_id = sub.id WHERE r.student_id = ? AND r.exam_term = ?");
$stmt->execute([$student_id, $exam_term]);
$results = $stmt->fetchAll();

// Fetch Attendance Stats
$stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) as present FROM attendance WHERE student_id = ?");
$stmt->execute([$student_id]);
$attendance = $stmt->fetch();
$att_percent = ($attendance['total'] > 0) ? round(($attendance['present'] / $attendance['total']) * 100) : 0;

function getGrade($marks, $total) {
    if ($total <= 0) return '-';
    $p = ($marks / $total) * 100;
    if ($p >= 90) return 'A+';
    if ($p >= 80) return 'A';
    if ($p >= 70) return 'B+';
    if ($p >= 60) return 'B';
    if ($p >= 50) return 'C+';
    if ($p >= 40) return 'C';
    return 'D';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Results - Everest Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .portal-nav {
            background: var(--glass);
            backdrop-filter: var(--blur);
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .portal-brand {
            display: flex;
            align-items: center;gap: 1rem;text-decoration: none;color: var(--text-main);
        }
        .portal-brand h2 {
            font-size: 1.25rem;font-weight: 800;letter-spacing: -0.025em;
        }
        .student-details {
            display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; background: var(--card-bg); padding: 2rem; border-radius: 12px; border: 1px solid var(--border);
        }
        .student-details div { font-size: 0.95rem; color: var(--text-muted); }
        .student-details span { font-weight: 600; color: var(--text-main); margin-left: 0.5rem;}
        @media print {
            .portal-nav, .btn-back, .print-btn { display: none !important; }
            body { background: white !important; color: black !important; }
            .glass-card { border: 1px solid #ccc !important; box-shadow: none !important; background: white !important; color: black !important;}
            .student-details { background: #f8f9fa !important; border: 1px solid #eee !important; }
            .student-details span, .student-details div { color: black !important; }
            .premium-table th { background: #f1f5f9 !important; color: black !important; }
            .premium-table td { color: black !important; }
        }
    </style>
</head>
<body style="background: var(--bg-color); color: var(--text-main); transition: var(--transition);">

<nav class="portal-nav">
    <a href="portal_dashboard.php" class="portal-brand">
        <i class="fa-solid fa-graduation-cap fa-2x" style="color: var(--secondary);"></i>
        <h2>Everest Portal</h2>
    </a>
    <div class="user-menu">
        <a href="logout_portal.php" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i></a>
    </div>
</nav>

<div class="container">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: var(--text-main);"><i class="fa-solid fa-chart-line" style="color: var(--secondary); margin-right: 0.75rem;"></i> Academic Results</h1>
        <a href="portal_dashboard.php" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <div class="glass-card" id="printableArea" style="padding: 3rem;">
        <div class="rc-header" style="text-align: center; margin-bottom: 3rem; border-bottom: 1px solid var(--border); padding-bottom: 2rem;">
            <h2 style="color: var(--secondary); font-size: 1.75rem; font-weight: 800; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($student['name']); ?>'s Report Card</h2>
            <p style="color: var(--text-muted); font-size: 1.1rem;"><?php echo htmlspecialchars($exam_term); ?> Examination</p>
            <h3 style="margin-top: 1rem; font-size: 1.25rem; color: var(--text-main); letter-spacing: 0.05em;">EVEREST INTERNATIONAL SCHOOL</h3>
        </div>
        
        <div class="student-details">
            <div>Student Name: <span><?php echo htmlspecialchars($student['name']); ?></span></div>
            <div>Class/Section: <span><?php echo htmlspecialchars($student['class_name']); ?></span></div>
            <div>Roll Number: <span><?php echo htmlspecialchars($student['roll_no']); ?></span></div>
            <div>Attendance: <span><?php echo $att_percent; ?>% (<?php echo $attendance['present']; ?>/<?php echo $attendance['total']; ?> Days)</span></div>
        </div>
        
        <div class="premium-table-container" style="margin-bottom: 3rem;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Subjects</th>
                        <th>Full Marks</th>
                        <th>Pass Marks</th>
                        <th>Marks Obtained</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_obtained = 0;
                    $total_full = 0;
                    foreach($results as $r): 
                        $total_obtained += $r['marks_obtained'];
                        $total_full += $r['total_marks'];
                        $grade = getGrade($r['marks_obtained'], $r['total_marks']);
                    ?>
                    <tr>
                        <td style="font-weight: 600; color: var(--text-main);"><?php echo htmlspecialchars($r['subject_name']); ?></td>
                        <td><?php echo $r['total_marks']; ?></td>
                        <td><?php echo $r['total_marks'] * 0.4; ?></td>
                        <td style="font-weight: 700; color: var(--secondary);"><?php echo $r['marks_obtained']; ?></td>
                        <td><span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.25rem 0.75rem; border-radius: 4px; font-weight: 700;"><?php echo $grade; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($results)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #94a3b8; padding: 3rem;">No results published for this term yet.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="summary-box" style="display: flex; justify-content: flex-end; gap: 3rem; font-size: 1.25rem; color: #94a3b8;">
            <?php if (!empty($results)): ?>
            <div>Total: <span style="color: white; font-weight: 800;"><?php echo $total_obtained; ?> / <?php echo $total_full; ?></span></div>
            <div>Percentage: <span style="color: var(--secondary); font-weight: 800;"><?php echo round(($total_obtained / $total_full) * 100, 1); ?>%</span></div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 5rem; display: flex; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 2rem; color: #64748b; font-size: 0.9rem;">
            <div style="text-align: center;">
                <div style="border-bottom: 1px solid #334155; width: 150px; margin-bottom: 0.5rem;"></div>
                Class Teacher
            </div>
            <div style="text-align: center;">
                <div style="border-bottom: 1px solid #334155; width: 170px; margin-bottom: 0.5rem; height: 18px; color: white;">Dr. Anita Thapa</div>
                Principal
            </div>
            <div style="text-align: center;">
                <div style="border-bottom: 1px solid #334155; width: 150px; margin-bottom: 0.5rem;"></div>
                Guardian Signature
            </div>
        </div>
    </div>
    
    <button onclick="window.print()" class="print-btn"><i class="fa-solid fa-print" style="margin-right: 0.5rem;"></i> Print Report Card</button>
</div>

</body>
</html>
