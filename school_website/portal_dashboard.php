<?php
session_start();
if(!isset($_SESSION['user_logged_in']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit;
}

require_once 'config/db.php';
$student_id = $_SESSION['user_id']; // In a real app, query the 'students' table with this user_id

$user_id = $_SESSION['user_id'];

// Fetch student details
$stmt = $pdo->prepare("SELECT s.*, c.class_name FROM students s JOIN classes c ON s.class_id = c.id WHERE s.user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch();

if (!$student) {
    echo "Student profile not found.";
    exit;
}

$student_name = $student['name'];
$student_class = $student['class_name'];
$roll_no = $student['roll_no'];
$student_id_real = $student['id'];

// Fetch Attendance Stats
$stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) as present FROM attendance WHERE student_id = ?");
$stmt->execute([$student_id_real]);
$attendance = $stmt->fetch();
$attendance_percentage = ($attendance['total'] > 0) ? round(($attendance['present'] / $attendance['total']) * 100) : 0;

// Fetch Pending Assignments
$stmt = $pdo->prepare("SELECT COUNT(*) FROM assignments WHERE class_id = ? AND due_date >= CURDATE()");
$stmt->execute([$student['class_id']]);
$pending_assignments = $stmt->fetchColumn();

// Fetch Latest Term Result (dummy grade for now if no results)
$stmt = $pdo->prepare("SELECT marks_obtained, total_marks FROM results WHERE student_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$student_id_real]);
$last_result = $stmt->fetch();
$last_grade = $last_result ? 'Calculated' : 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Everest School</title>
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
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: var(--text-main);
        }
        .portal-brand h2 {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.025em;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        .user-menu img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid var(--primary);
        }
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: var(--radius-lg);
            padding: 3rem;
            margin: 2rem 0;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-lg);
        }
    </style>
</head>
<body style="background: var(--bg-color); color: var(--text-main); transition: var(--transition);">

<nav class="portal-nav">
    <a href="index.php" class="portal-brand">
        <i class="fa-solid fa-graduation-cap fa-2x" style="color: var(--secondary);"></i>
        <h2>Everest Portal</h2>
    </a>
    <div class="user-menu">
        <div style="text-align: right; display: none; @media(min-width: 600px){display: block;}">
            <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-main);"><?php echo htmlspecialchars($student_name); ?></div>
            <div style="font-size: 0.8rem; color: var(--text-muted);">Student | <?php echo htmlspecialchars($student_class); ?></div>
        </div>
        <img src="https://ui-avatars.com/api/?name=Aarav+Sharma&background=0D8ABC&color=fff" alt="User">
        <a href="logout_portal.php" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i></a>
    </div>
</nav>

<div class="container">
    <div class="welcome-banner">
        <div class="student-info">
            <h1>Welcome back, <?php echo htmlspecialchars(explode(' ', $student_name)[0]); ?>!</h1>
            <p><i class="fa-solid fa-id-card" style="margin-right: 0.5rem;"></i> Roll No: <?php echo htmlspecialchars($roll_no); ?> | Class: <?php echo htmlspecialchars($student_class); ?></p>
        </div>
        <div style="text-align: center; background: rgba(255,255,255,0.2); padding: 1rem 2rem; border-radius: 8px;">
            <div style="font-size: 2rem; font-weight: 700;"><?php echo $attendance_percentage; ?>%</div>
            <div style="font-size: 0.9rem; opacity: 0.9;">Total Attendance</div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Academic Summary -->
        <div class="reveal glass-card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h3 style="font-weight: 800; color: var(--text-main);">Academic Progress</h3>
                <i class="fa-solid fa-chart-line" style="color: var(--secondary);"></i>
            </div>
            <div class="stat-row" style="display: flex; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <span class="stat-label" style="color: #94a3b8;">Last Exam (First Term)</span>
                <span class="stat-value" style="color: var(--secondary); font-weight: 700;">Grade: A</span>
            </div>
            <div class="stat-row" style="display: flex; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <span class="stat-label" style="color: #94a3b8;">Pending Assignments</span>
                <span class="stat-value" style="color: #f87171; font-weight: 700;"><?php echo $pending_assignments; ?> Due</span>
            </div>
            <div class="stat-row" style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                <span class="stat-label" style="color: var(--text-muted);">Current Rank</span>
                <span class="stat-value" style="color: var(--text-main); font-weight: 700;">5th</span>
            </div>
            <a href="portal_results.php" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">
                View Detailed Report Card
            </a>
        </div>

        <!-- Quick Links -->
        <div class="reveal glass-card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h3 style="font-weight: 800; color: white;">Student Services</h3>
                <i class="fa-solid fa-layer-group" style="color: var(--primary);"></i>
            </div>
            <div class="action-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <a href="portal_assignments.php" class="btn btn-primary btn-sm" style="flex-direction: column; gap: 0.5rem; padding: 1.5rem;">
                    <i class="fa-solid fa-book-open"></i>
                    <span>Assignments</span>
                </a>
                <a href="portal_timetable.php" class="btn btn-secondary btn-sm" style="flex-direction: column; gap: 0.5rem; padding: 1.5rem;">
                    <i class="fa-regular fa-calendar-days"></i>
                    <span>Timetable</span>
                </a>
                <a href="#" class="btn btn-primary btn-sm" style="flex-direction: column; gap: 0.5rem; padding: 1.5rem; opacity: 0.6; pointer-events: none;">
                    <i class="fa-solid fa-book"></i>
                    <span>E-Library</span>
                </a>
                <a href="#" class="btn btn-secondary btn-sm" style="flex-direction: column; gap: 0.5rem; padding: 1.5rem; opacity: 0.6; pointer-events: none;">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>Fee Status</span>
                </a>
            </div>
        </div>

        <!-- Notifications -->
        <div class="reveal glass-card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h3 style="font-weight: 800; color: var(--text-main);">Latest Notices</h3>
                <i class="fa-regular fa-bell" style="color: var(--accent);"></i>
            </div>
            <?php
            $stmt = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC LIMIT 2");
            $notices = $stmt->fetchAll();
            foreach ($notices as $n):
            ?>
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <div style="width: 40px; height: 40px; background: rgba(56, 189, 248, 0.1); color: #38bdf8; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"><i class="fa-solid fa-bullhorn"></i></div>
                <div>
                    <h4 style="font-size: 0.95rem; color: var(--text-main); margin-bottom: 0.25rem;"><?php echo htmlspecialchars($n['title']); ?></h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;"><?php echo substr(htmlspecialchars($n['content']), 0, 80) . '...'; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($notices)): ?>
                <p style="color: #94a3b8; text-align: center; padding: 2rem;">No recent notices.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
