<?php
session_start();
require_once '../config/db.php';

$active_page = 'assignments';
$page_title = 'Manage Assignments';
$message = '';

// Handle Add Assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_assignment') {
    $class_id = $_POST['class_id'];
    $subject_id = $_POST['subject_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    
    $file_url = '';
    if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] == 0) {
        $target_dir = "../uploads/assignments/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_name = time() . '_' . basename($_FILES["assignment_file"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $target_file)) {
            $file_url = "uploads/assignments/" . $file_name;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO assignments (class_id, subject_id, title, description, file_url, due_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$class_id, $subject_id, $title, $description, $file_url, $due_date]);
    $message = "Assignment posted successfully!";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM assignments WHERE id = ?")->execute([$id]);
    header("Location: assignments_manage.php");
    exit;
}

// Fetch Classes and Subjects
$classes = $pdo->query("SELECT * FROM classes")->fetchAll();
$subjects = $pdo->query("SELECT * FROM subjects")->fetchAll();

// Fetch Assignments
$stmt = $pdo->query("SELECT a.*, c.class_name, s.subject_name FROM assignments a JOIN classes c ON a.class_id = c.id JOIN subjects s ON a.subject_id = s.id ORDER BY a.created_at DESC");
$assignments = $stmt->fetchAll();

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
        <div style="width: 50px; height: 50px; background: rgba(79, 70, 229, 0.1); color: #818cf8; border-radius: 14px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(79, 70, 229, 0.2);">
            <i class="fa-solid fa-file-signature"></i>
        </div>
        <div>
            <h3 style="margin: 0; font-weight: 800; color: white;">Publish Scholastic Tasks</h3>
            <p style="margin: 0.25rem 0 0; color: #94a3b8; font-size: 0.9rem;">Assign new directives to academic tiers.</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add_assignment">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Target Academic Tier</label>
                <select name="class_id" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; color: white; outline: none; cursor: pointer;" required>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo $c['class_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Curriculum Subject</label>
                <select name="subject_id" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; color: white; outline: none; cursor: pointer;" required>
                    <?php foreach ($subjects as $s): ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo $s['subject_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Assignment Designation</label>
                <input type="text" name="title" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; color: white; outline: none;" placeholder="e.g. Theoretical Physics Module 4" required>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Logistical Directives / Instructions</label>
                <textarea name="description" rows="4" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; color: white; outline: none; resize: none;" placeholder="Provide clear technical directives for the students..." required></textarea>
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Deadline Verification</label>
                <input type="date" name="due_date" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.9rem; border-radius: 12px; color: white; outline: none;" required>
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Resource Attachment</label>
                <input type="file" name="assignment_file" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.75rem; border-radius: 12px; color: #94a3b8;">
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem; margin-top: 1.5rem;">
            <i class="fa-solid fa-paper-plane"></i> Finalize Task Distribution
        </button>
    </form>
</div>

<div class="card" style="padding: 0; margin-top: 4rem;">
    <div style="padding: 2.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; gap: 1rem;">
        <div style="width: 45px; height: 45px; background: rgba(16, 185, 129, 0.1); color: var(--secondary); border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(16, 185, 129, 0.2);">
            <i class="fa-solid fa-list-check"></i>
        </div>
        <h3 style="margin: 0; font-weight: 800; color: white;">Live Directive Log</h3>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th style="padding-left: 2.5rem;">Effective Deadline</th>
                    <th>Academic Tier</th>
                    <th>Subject</th>
                    <th>Directive Title</th>
                    <th style="padding-right: 2.5rem; text-align: center;">Manage</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $a): ?>
                <tr>
                    <td style="padding-left: 2.5rem;">
                        <div style="font-weight: 800; color: #ef4444; font-family: monospace; font-size: 1rem;"><?php echo date('M d, Y', strtotime($a['due_date'])); ?></div>
                    </td>
                    <td><span style="font-weight: 700; color: white;"><?php echo htmlspecialchars($a['class_name']); ?></span></td>
                    <td><span style="font-weight: 600; color: #94a3b8;"><?php echo htmlspecialchars($a['subject_name']); ?></span></td>
                    <td><span style="font-weight: 700; color: var(--secondary);"><?php echo htmlspecialchars($a['title']); ?></span></td>
                    <td style="padding-right: 2.5rem; text-align: center;">
                        <a href="?delete=<?php echo $a['id']; ?>" style="color: #ef4444; font-size: 1.1rem; padding: 0.5rem; transition: 0.3s;" onmouseover="this.style.color='#f87171'" onmouseout="this.style.color='#ef4444'" onclick="return confirm('Revoke this directive?');">
                            <i class="fa-solid fa-trash-can"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($assignments)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 6rem; color: #64748b;">
                        <i class="fa-solid fa-file-circle-exclamation fa-3x" style="display: block; margin-bottom: 1.5rem; opacity: 0.2;"></i>
                        No active directives found.
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
