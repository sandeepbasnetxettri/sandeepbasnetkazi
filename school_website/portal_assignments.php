<?php
session_start();
if(!isset($_SESSION['user_logged_in']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit;
}
require_once 'config/db.php';

$user_id = $_SESSION['user_id'];

// Fetch student info
$stmt = $pdo->prepare("SELECT s.*, c.id as class_id, c.class_name FROM students s JOIN classes c ON s.class_id = c.id WHERE s.user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch();

if (!$student) {
    echo "Student profile not found.";
    exit;
}

$class_id = $student['class_id'];

// Fetch Assignments for this class
$stmt = $pdo->prepare("SELECT a.*, s.subject_name FROM assignments a JOIN subjects s ON a.subject_id = s.id WHERE a.class_id = ? ORDER BY a.due_date ASC");
$stmt->execute([$class_id]);
$assignments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homework & Assignments - Everest Portal</title>
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
        .as-tag {
            padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
        }
        .tag-subject { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .tag-date { background: rgba(248, 113, 113, 0.1); color: #f87171; }
        .file-link {
            display: inline-flex; align-items: center; gap: 0.5rem; margin-top: 1.5rem; padding: 0.75rem 1.25rem; background: var(--glass); color: var(--text-main); text-decoration: none; border-radius: 8px; font-weight: 600; border: 1px solid var(--glass-border); transition: 0.3s;
        }
        .file-link:hover { background: rgba(255,255,255,0.1); border-color: var(--secondary); color: var(--secondary); }
    </style>
</head>
<body style="background: var(--bg-color); color: var(--text-main); transition: var(--transition);">

<nav class="portal-nav">
    <a href="portal_dashboard.php" class="portal-brand">
        <i class="fa-solid fa-graduation-cap fa-2x" style="color: var(--secondary);"></i>
        <h2>Everest Portal</h2>
    </a>
</nav>

<div class="container section-padding">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <h1 style="color: var(--text-main);"><i class="fa-solid fa-book" style="color: var(--secondary); margin-right: 0.75rem;"></i> My Assignments</h1>
        <a href="portal_dashboard.php" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <?php if (empty($assignments)): ?>
        <div class="reveal glass-card" style="text-align: center; padding: 5rem;">
            <i class="fa-solid fa-face-smile-wink fa-4x" style="margin-bottom: 2rem; color: var(--secondary);"></i>
            <h3 style="color: var(--text-main); font-weight: 800;">No active assignments for your class!</h3>
            <p style="color: var(--text-muted); margin-top: 1rem;">Keep up the great work and stay ahead of your studies.</p>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
        <?php foreach ($assignments as $a): ?>
        <div class="reveal glass-card">
            <div class="as-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <span class="as-tag tag-subject"><?php echo htmlspecialchars($a['subject_name']); ?></span>
                    <h2 style="margin-top: 1rem; color: var(--text-main); font-weight: 800; font-size: 1.5rem;"><?php echo htmlspecialchars($a['title']); ?></h2>
                </div>
                <span class="as-tag tag-date"><i class="fa-regular fa-clock" style="margin-right: 0.5rem;"></i> Due: <?php echo date('M d, Y', strtotime($a['due_date'])); ?></span>
            </div>
            <p style="color: var(--text-muted); line-height: 1.8; font-size: 1.05rem;"><?php echo nl2br(htmlspecialchars($a['description'])); ?></p>
            
            <?php if ($a['file_url']): ?>
                <a href="<?php echo $a['file_url']; ?>" target="_blank" class="file-link">
                    <i class="fa-solid fa-paperclip"></i> Download Study Materials
                </a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
