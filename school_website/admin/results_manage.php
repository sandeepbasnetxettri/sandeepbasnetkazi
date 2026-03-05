<?php
session_start();
require_once '../config/db.php';

$active_page = 'results';
$page_title = 'Exam Results Management';
$message = '';

$class_id = $_GET['class_id'] ?? null;
$subject_id = $_GET['subject_id'] ?? null;
$exam_term = $_GET['exam_term'] ?? 'Final Term';

// Handle Add Subject
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_subject') {
    $subject_name = $_POST['subject_name'];
    $stmt = $pdo->prepare("INSERT INTO subjects (class_id, subject_name) VALUES (?, ?)");
    $stmt->execute([$class_id, $subject_name]);
    $message = "Subject added successfully!";
}

// Handle Save Results
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_results') {
    $marks = $_POST['marks'] ?? [];
    $total_marks = $_POST['total_marks'] ?? 100;
    
    $pdo->beginTransaction();
    try {
        foreach ($marks as $student_id => $mark) {
            $stmt = $pdo->prepare("INSERT INTO results (student_id, subject_id, exam_term, marks_obtained, total_marks) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE marks_obtained = ?");
            $stmt->execute([$student_id, $subject_id, $exam_term, $mark, $total_marks, $mark]);
        }
        $pdo->commit();
        $message = "Marks saved successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch Classes
$classes = $pdo->query("SELECT * FROM classes")->fetchAll();

// Fetch Subjects for selected class
$subjects = [];
if ($class_id) {
    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE class_id = ?");
    $stmt->execute([$class_id]);
    $subjects = $stmt->fetchAll();
}

// Fetch Students and existing marks
$students = [];
if ($class_id && $subject_id) {
    $stmt = $pdo->prepare("SELECT s.*, r.marks_obtained FROM students s LEFT JOIN results r ON s.id = r.student_id AND r.subject_id = ? AND r.exam_term = ? WHERE s.class_id = ?");
    $stmt->execute([$subject_id, $exam_term, $class_id]);
    $students = $stmt->fetchAll();
}

require_once 'includes/header.php';
?>

<?php if ($message): ?>
    <div id="statusMessage" style="background: var(--secondary); color: white; padding: 1.25rem 2rem; border-radius: 12px; margin-bottom: 3rem; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(255,255,255,0.1); animation: slideIn 0.4s ease;">
        <div style="display: flex; align-items: center; gap: 1rem; font-weight: 700;">
            <i class="fa-solid fa-cloud-check"></i>
            <?php echo $message; ?>
        </div>
        <i class="fa-solid fa-xmark cursor-pointer" onclick="this.parentElement.remove()"></i>
    </div>
<?php endif; ?>

<div class="card" style="margin-bottom: 3rem;">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2.5rem;">
        <div style="width: 45px; height: 45px; background: rgba(79, 70, 229, 0.1); color: #818cf8; border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(79, 70, 229, 0.2);">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <h3 style="margin: 0; font-weight: 800; color: white;">Academic Assessment Config</h3>
    </div>
    <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 2rem;">
        <div class="form-group">
            <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Academic Tier</label>
            <select name="class_id" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; color: white; outline: none; cursor: pointer;" onchange="this.form.subject_id.value=''; this.form.submit()">
                <option value="">-- Select Class --</option>
                <?php foreach ($classes as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo ($class_id == $c['id']) ? 'selected' : ''; ?>><?php echo $c['class_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <?php if ($class_id): ?>
        <div class="form-group">
            <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Curriculum Subject</label>
            <select name="subject_id" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; color: white; outline: none; cursor: pointer;" onchange="this.form.submit()">
                <option value="">-- Select Subject --</option>
                <?php foreach ($subjects as $sub): ?>
                    <option value="<?php echo $sub['id']; ?>" <?php echo ($subject_id == $sub['id']) ? 'selected' : ''; ?>><?php echo $sub['subject_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Assessment Term</label>
            <select name="exam_term" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; color: white; outline: none; cursor: pointer;" onchange="this.form.submit()">
                <option value="First Term" <?php echo ($exam_term == 'First Term') ? 'selected' : ''; ?>>First Term</option>
                <option value="Second Term" <?php echo ($exam_term == 'Second Term') ? 'selected' : ''; ?>>Second Term</option>
                <option value="Final Term" <?php echo ($exam_term == 'Final Term') ? 'selected' : ''; ?>>Final Term</option>
            </select>
        </div>
        <?php endif; ?>
    </form>
</div>

<?php if ($class_id && !$subject_id): ?>
<div class="card" style="border-left: 4px solid var(--primary);">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
        <i class="fa-solid fa-plus-circle" style="color: var(--primary);"></i>
        <h3 style="margin: 0; font-weight: 800; color: white;">Define Subject</h3>
    </div>
    <form method="POST" style="display: flex; gap: 1rem;">
        <input type="hidden" name="action" value="add_subject">
        <input type="text" name="subject_name" style="flex: 1; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.9rem 1.25rem; border-radius: 12px; color: white; outline: none;" placeholder="New Subject Name" required>
        <button type="submit" class="btn btn-primary" style="white-space: nowrap;">Register Subject</button>
    </form>
</div>
<?php endif; ?>

<?php if ($class_id && $subject_id): ?>
<div class="card" style="padding: 0;">
    <div style="padding: 2.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
        <h3 style="margin: 0; font-weight: 800; color: white;">Marks Transcription Portal</h3>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <label style="font-weight: 700; color: #94a3b8; font-size: 0.85rem; text-transform: uppercase;">Scaling Base:</label>
            <input type="number" form="marksForm" name="total_marks" style="width: 80px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.05); padding: 0.6rem; border-radius: 8px; color: white; text-align: center; font-weight: 700;" value="100">
        </div>
    </div>
    <form method="POST" id="marksForm">
        <input type="hidden" name="action" value="save_results">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th style="padding-left: 2.5rem;">Roll ID</th>
                        <th>Student Personnel</th>
                        <th style="padding-right: 2.5rem; text-align: right; width: 220px;">Marks Achieved</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                    <tr>
                        <td style="padding-left: 2.5rem; font-weight: 800; color: var(--secondary);"><?php echo htmlspecialchars($s['roll_no']); ?></td>
                        <td>
                            <div style="font-weight: 700; color: white;"><?php echo htmlspecialchars($s['name']); ?></div>
                        </td>
                        <td style="padding-right: 2.5rem;">
                            <input type="number" step="0.5" name="marks[<?php echo $s['id']; ?>]" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.75rem 1rem; border-radius: 8px; color: white; text-align: right; font-weight: 700; font-family: monospace; font-size: 1.1rem; outline: none;" value="<?php echo $s['marks_obtained']; ?>" placeholder="0.0">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="padding: 2rem 2.5rem; border-top: 1px solid rgba(255,255,255,0.05); text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 1rem 2.5rem;">
                <i class="fa-solid fa-server"></i> Finalize Assessment Data
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
