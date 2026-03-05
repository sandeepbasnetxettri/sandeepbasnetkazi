<?php
session_start();
require_once '../config/db.php';

$active_page = 'attendance';
$page_title = 'Attendance Management';
$message = '';

$class_id = $_GET['class_id'] ?? null;
$date = $_GET['date'] ?? date('Y-m-d');

// Handle Saving Attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_attendance') {
    $statuses = $_POST['status'] ?? [];
    $pdo->beginTransaction();
    try {
        foreach ($statuses as $student_id => $status) {
            $stmt = $pdo->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE status = ?");
            $stmt->execute([$student_id, $date, $status, $status]);
        }
        $pdo->commit();
        $message = "Attendance saved successfully for " . $date;
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch Classes
$classes = $pdo->query("SELECT * FROM classes")->fetchAll();

// Fetch Students for selected class
$students = [];
if ($class_id) {
    $stmt = $pdo->prepare("SELECT s.*, a.status as current_status FROM students s LEFT JOIN attendance a ON s.id = a.student_id AND a.date = ? WHERE s.class_id = ?");
    $stmt->execute([$date, $class_id]);
    $students = $stmt->fetchAll();
}

require_once 'includes/header.php';
?>

<?php if ($message): ?>
    <div id="statusMessage" style="background: var(--secondary); color: white; padding: 1.25rem 2rem; border-radius: 12px; margin-bottom: 3rem; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(255,255,255,0.1); animation: slideIn 0.4s ease;">
        <div style="display: flex; align-items: center; gap: 1rem; font-weight: 700;">
            <i class="fa-solid fa-circle-check"></i>
            <?php echo $message; ?>
        </div>
        <i class="fa-solid fa-xmark cursor-pointer" onclick="this.parentElement.remove()"></i>
    </div>
<?php endif; ?>

<div class="card" style="margin-bottom: 3rem;">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2.5rem;">
        <div style="width: 45px; height: 45px; background: rgba(79, 70, 229, 0.1); color: #818cf8; border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(79, 70, 229, 0.2);">
            <i class="fa-solid fa-clipboard-user"></i>
        </div>
        <h3 style="margin: 0; font-weight: 800; color: white;">Logistical Configuration</h3>
    </div>
    <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
        <div class="form-group">
            <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Academic Tier</label>
            <select name="class_id" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; color: white; outline: none; cursor: pointer;" onchange="this.form.submit()">
                <option value="">-- Choose Class --</option>
                <?php foreach ($classes as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo ($class_id == $c['id']) ? 'selected' : ''; ?>><?php echo $c['class_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Verification Date</label>
            <input type="date" name="date" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.9rem; border-radius: 12px; color: white; outline: none;" value="<?php echo $date; ?>" onchange="this.form.submit()">
        </div>
    </form>
</div>

<?php if ($class_id): ?>
<div class="card" style="padding: 0;">
    <div style="padding: 2.5rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
        <h3 style="margin: 0; font-weight: 800; color: white;">Personnel Verification Roll</h3>
    </div>
    <form method="POST">
        <input type="hidden" name="action" value="save_attendance">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th style="padding-left: 2.5rem;">Roll ID</th>
                        <th>Student Personnel</th>
                        <th style="padding-right: 2.5rem; text-align: center;">Presence Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                    <tr>
                        <td style="padding-left: 2.5rem; font-weight: 800; color: var(--secondary);"><?php echo htmlspecialchars($s['roll_no']); ?></td>
                        <td>
                            <div style="font-weight: 700; color: white;"><?php echo htmlspecialchars($s['name']); ?></div>
                        </td>
                        <td style="padding-right: 2.5rem; text-align: center;">
                            <select name="status[<?php echo $s['id']; ?>]" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.05); padding: 0.6rem 1rem; border-radius: 8px; color: white; font-weight: 600; cursor: pointer;">
                                <option value="present" <?php echo ($s['current_status'] == 'present') ? 'selected' : ''; ?>>PRESENT</option>
                                <option value="absent" <?php echo ($s['current_status'] == 'absent') ? 'selected' : ''; ?>>ABSENT</option>
                                <option value="late" <?php echo ($s['current_status'] == 'late') ? 'selected' : ''; ?>>LATE</option>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="padding: 2rem 2.5rem; border-top: 1px solid rgba(255,255,255,0.05); text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 1rem 2rem;">
                <i class="fa-solid fa-shield-check"></i> Finalize Register
            </button>
        </div>
    </form>
</div>
<?php else: ?>
    <div class="card" style="text-align: center; color: #64748b; padding: 6rem;">
        <i class="fa-solid fa-layer-group fa-4x" style="margin-bottom: 2rem; opacity: 0.2;"></i>
        <h3 style="font-weight: 800; color: white;">Select Parameters</h3>
        <p style="margin-top: 0.5rem;">Choose an academic tier to initiate verification.</p>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
