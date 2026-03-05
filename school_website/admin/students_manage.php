<?php
session_start();
require_once '../config/db.php';

$active_page = 'students';
$page_title = 'Manage Students';
$message = '';

// Handle Add Student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_student') {
    $name = $_POST['name'];
    $roll_no = $_POST['roll_no'];
    $class_id = $_POST['class_id'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();
        
        // Create user account
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'student')");
        $stmt->execute([$username, $password]);
        $user_id = $pdo->lastInsertId();
        
        // Create student profile
        $stmt = $pdo->prepare("INSERT INTO students (user_id, roll_no, name, class_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $roll_no, $name, $class_id]);
        
        $pdo->commit();
        $message = "Student added successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Error: " . $e->getMessage();
    }
}

// Handle Delete Student
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT user_id FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $user_id = $stmt->fetchColumn();
    
    if ($user_id) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);
        // Foreign key cascade or manual delete for students table if not cascade
        $pdo->prepare("DELETE FROM students WHERE id = ?")->execute([$id]);
    }
    header("Location: students_manage.php");
    exit;
}

// Fetch Classes for dropdown
$classes = $pdo->query("SELECT * FROM classes")->fetchAll();

// Fetch Students
$stmt = $pdo->query("SELECT s.*, c.class_name FROM students s JOIN classes c ON s.class_id = c.id ORDER BY c.id, s.roll_no");
$students = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<style>
    .search-box { position: relative; margin-bottom: 2rem; }
    .search-box i {
        position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%);
        color: #94a3b8; font-size: 1.1rem;
    }
    .badge-student {
        background: rgba(79, 70, 229, 0.1); color: #818cf8; border: 1px solid rgba(79, 70, 229, 0.2);
        padding: 0.4rem 0.8rem; border-radius: 50px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;
    }
</style>

<?php if ($message): ?>
    <div id="statusMessage" style="background: var(--secondary); color: white; padding: 1.25rem 2rem; border-radius: 12px; margin-bottom: 3rem; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(255,255,255,0.1); animation: slideIn 0.4s ease;">
        <div style="display: flex; align-items: center; gap: 1rem; font-weight: 700;">
            <i class="fa-solid fa-user-shield"></i>
            <?php echo $message; ?>
        </div>
        <i class="fa-solid fa-xmark cursor-pointer" onclick="this.parentElement.remove()"></i>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 3rem; align-items: start;">
    <!-- Registration Form -->
    <div class="card">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2.5rem;">
            <div style="width: 50px; height: 50px; background: rgba(79,70,229,0.1); color: #818cf8; border-radius: 14px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(79,70,229,0.2);">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-weight: 800; color: white;">Enroll Student</h3>
                <p style="margin: 0.25rem 0 0; color: #94a3b8; font-size: 0.9rem;">Onboard new academic members.</p>
            </div>
        </div>

        <form method="POST" id="studentForm">
            <input type="hidden" name="action" value="add_student">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Full Legal Name</label>
                <input type="text" name="name" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.9rem; border-radius: 12px; color: white; outline: none;" placeholder="e.g. Johnathan Doe" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Roll Number</label>
                    <input type="text" name="roll_no" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.9rem; border-radius: 12px; color: white; outline: none;" placeholder="e.g. 101-A" required>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Class Tier</label>
                    <select name="class_id" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.9rem; border-radius: 12px; color: white; outline: none; cursor: pointer;" required>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>"><?php echo $class['class_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Institutional Username</label>
                <input type="text" name="username" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.9rem; border-radius: 12px; color: white; outline: none;" placeholder="portal_id" required>
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Security Passphrase</label>
                <input type="password" name="password" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.9rem; border-radius: 12px; color: white; outline: none;" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem;">
                <i class="fa-solid fa-id-card-clip"></i> Finalize Enrollment
            </button>
        </form>
    </div>

    <!-- Student List -->
    <div class="card" style="padding: 0; flex: 1.2;">
        <div style="padding: 2.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.1); color: var(--secondary); border-radius: 14px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(16, 185, 129, 0.2);">
                    <i class="fa-solid fa-users-viewfinder"></i>
                </div>
                <h3 style="margin: 0; font-weight: 800; color: white;">Student Registry</h3>
            </div>
            <span style="font-size: 0.85rem; font-weight: 700; color: #94a3b8; background: rgba(255,255,255,0.05); padding: 0.6rem 1.25rem; border-radius: 50px; border: 1px solid rgba(255,255,255,0.05); display: inline-flex; align-items: center; gap: 0.5rem;">
                <span id="studentCount" style="color: white;"><?php echo count($students); ?></span> PROFILES
            </span>
        </div>

        <div style="padding: 2rem 2.5rem 0;">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="studentSearch" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1rem 1rem 1rem 3.5rem; border-radius: 12px; color: white; outline: none;" placeholder="Query by name, roll no, or class tier...">
            </div>
        </div>

        <div class="table-container">
            <table class="table" id="studentTable">
                <thead>
                    <tr>
                        <th style="padding-left: 2.5rem;">Roll ID</th>
                        <th>Student Personnel</th>
                        <th>Tier</th>
                        <th style="padding-right: 2.5rem; text-align: center;">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                    <tr class="student-row">
                        <td style="padding-left: 2.5rem;">
                            <div style="font-weight: 800; color: var(--secondary); font-size: 1.1rem;"><?php echo htmlspecialchars($s['roll_no']); ?></div>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: white; margin-bottom: 0.2rem;"><?php echo htmlspecialchars($s['name']); ?></div>
                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; letter-spacing: 0.05em;">#<?php echo str_pad($s['id'], 5, '0', STR_PAD_LEFT); ?></div>
                        </td>
                        <td>
                            <span class="badge-student"><?php echo htmlspecialchars($s['class_name']); ?></span>
                        </td>
                        <td style="padding-right: 2.5rem; text-align: center;">
                            <a href="?delete=<?php echo $s['id']; ?>" style="color: #ef4444; font-size: 1.1rem; padding: 0.5rem; transition: 0.3s;" onmouseover="this.style.color='#f87171'; transform: scale(1.1);" onmouseout="this.style.color='#ef4444'; transform: scale(1);" onclick="return confirm('Revoke institutional access for this student?');">
                                <i class="fa-solid fa-user-minus"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($students)): ?>
                        <tr><td colspan="4" style="text-align: center; padding: 6rem; color: #64748b;">
                            <i class="fa-solid fa-user-slash fa-3x" style="display: block; margin-bottom: 1.5rem; opacity: 0.2;"></i>
                            No student profiles found.
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('studentSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#studentTable tbody tr.student-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if(text.includes(term)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        document.getElementById('studentCount').textContent = visibleCount;
    });

    document.getElementById('studentForm').onsubmit = function() {
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Authorizing...';
        btn.style.opacity = '0.8';
        btn.style.pointerEvents = 'none';
    };
</script>

<?php require_once 'includes/footer.php'; ?>
