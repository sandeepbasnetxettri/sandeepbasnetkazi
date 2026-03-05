<?php
session_start();
require_once '../config/db.php';

$active_page = 'teachers';
$page_title = 'Manage Teachers';
$message = '';

// Handle Add Teacher
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_teacher') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();
        
        // Create user account
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'teacher')");
        $stmt->execute([$username, $password]);
        $user_id = $pdo->lastInsertId();
        
        // Create teacher profile
        $stmt = $pdo->prepare("INSERT INTO teachers (user_id, name, email, phone, department) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $name, $email, $phone, $department]);
        
        $pdo->commit();
        $message = "Teacher added successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Error: " . $e->getMessage();
    }
}

// Handle Delete Teacher
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT user_id FROM teachers WHERE id = ?");
    $stmt->execute([$id]);
    $user_id = $stmt->fetchColumn();
    
    if ($user_id) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);
        $pdo->prepare("DELETE FROM teachers WHERE id = ?")->execute([$id]);
    }
    header("Location: teachers_manage.php");
    exit;
}

// Fetch Teachers
$stmt = $pdo->query("SELECT * FROM teachers ORDER BY name");
$teachers = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<style>
    .search-box { position: relative; margin-bottom: 2rem; }
    .search-box i {
        position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%);
        color: #94a3b8; font-size: 1.1rem;
    }
    .badge-faculty {
        background: rgba(16, 185, 129, 0.1); color: var(--secondary); border: 1px solid rgba(16, 185, 129, 0.2);
        padding: 0.4rem 0.8rem; border-radius: 50px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;
    }
</style>

<?php if ($message): ?>
    <div id="statusMessage" style="background: var(--secondary); color: white; padding: 1.25rem 2rem; border-radius: 12px; margin-bottom: 3rem; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(255,255,255,0.1); animation: slideIn 0.4s ease;">
        <div style="display: flex; align-items: center; gap: 1rem; font-weight: 700;">
            <i class="fa-solid fa-user-check"></i>
            <?php echo $message; ?>
        </div>
        <i class="fa-solid fa-xmark cursor-pointer" onclick="this.parentElement.remove()"></i>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 3rem; align-items: start;">
    <!-- Registration Form -->
    <div class="card">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2.5rem;">
            <div style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.1); color: var(--secondary); border-radius: 14px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(16, 185, 129, 0.2);">
                <i class="fa-solid fa-id-badge"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-weight: 800; color: white;">Appoint Teacher</h3>
                <p style="margin: 0.25rem 0 0; color: #94a3b8; font-size: 0.9rem;">Register new academic teachers.</p>
            </div>
        </div>

        <form method="POST" id="teacherForm">
            <input type="hidden" name="action" value="add_teacher">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Teacher Full Name</label>
                <input type="text" name="name" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.9rem; border-radius: 12px; color: white; outline: none;" placeholder="e.g. Dr. Satya Nadella" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Official Email</label>
                    <input type="email" name="email" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.9rem; border-radius: 12px; color: white; outline: none;" placeholder="name@everest.edu" required>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Department</label>
                    <input type="text" name="department" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.9rem; border-radius: 12px; color: white; outline: none;" placeholder="e.g. Physics & Astronomy">
                </div>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Secure Contact Number</label>
                <input type="text" name="phone" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.9rem; border-radius: 12px; color: white; outline: none;" placeholder="+977-98XXXXXXXX">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Portal ID</label>
                    <input type="text" name="username" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.9rem; border-radius: 12px; color: white; outline: none;" placeholder="staff_id" required>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Access Key</label>
                    <input type="password" name="password" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 0.9rem; border-radius: 12px; color: white; outline: none;" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem;">
                <i class="fa-solid fa-user-shield"></i> Initialize Teacher Profile
            </button>
        </form>
    </div>

    <!-- Teacher List -->
    <div class="card" style="padding: 0; flex: 1.2;">
        <div style="padding: 2.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 50px; height: 50px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-radius: 14px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(245, 158, 11, 0.2);">
                    <i class="fa-solid fa-address-book"></i>
                </div>
                <h3 style="margin: 0; font-weight: 800; color: white;">Teacher Registry</h3>
            </div>
            <span style="font-size: 0.85rem; font-weight: 700; color: #94a3b8; background: rgba(255,255,255,0.05); padding: 0.6rem 1.25rem; border-radius: 50px; border: 1px solid rgba(255,255,255,0.05); display: inline-flex; align-items: center; gap: 0.5rem;">
                <span id="teacherCount" style="color: white;"><?php echo count($teachers); ?></span> PERSONNEL
            </span>
        </div>

        <div style="padding: 2rem 2.5rem 0;">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="teacherSearch" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1rem 1rem 1rem 3.5rem; border-radius: 12px; color: white; outline: none;" placeholder="Query by name, department, or secure email...">
            </div>
        </div>

        <div class="table-container">
            <table class="table" id="teacherTable">
                <thead>
                    <tr>
                        <th style="padding-left: 2.5rem;">Teacher</th>
                        <th>Classification</th>
                        <th>Contact Matrix</th>
                        <th style="padding-right: 2.5rem; text-align: center;">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teachers as $t): ?>
                    <tr class="teacher-row">
                        <td style="padding-left: 2.5rem;">
                            <div style="font-weight: 800; color: white; margin-bottom: 0.2rem;"><?php echo htmlspecialchars($t['name']); ?></div>
                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; letter-spacing: 0.05em;">STAFF ID: #F<?php echo str_pad($t['id'], 4, '0', STR_PAD_LEFT); ?></div>
                        </td>
                        <td>
                            <span class="badge-faculty"><?php echo htmlspecialchars($t['department'] ?: 'Core Teacher'); ?></span>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; color: white; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-envelope-open" style="color: var(--secondary); font-size: 0.8rem;"></i> <?php echo htmlspecialchars($t['email']); ?>
                            </div>
                            <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 0.25rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-phone-flip" style="font-size: 0.75rem; color: var(--primary);"></i> <?php echo htmlspecialchars($t['phone'] ?: 'No primary contact'); ?>
                            </div>
                        </td>
                        <td style="padding-right: 2.5rem; text-align: center;">
                            <a href="?delete=<?php echo $t['id']; ?>" style="color: #ef4444; font-size: 1.1rem; padding: 0.5rem; transition: 0.3s;" onmouseover="this.style.color='#f87171'" onmouseout="this.style.color='#ef4444'" onclick="return confirm('Revoke institutional privileges for this teacher?');">
                                <i class="fa-solid fa-user-slash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($teachers)): ?>
                        <tr><td colspan="4" style="text-align: center; padding: 6rem; color: #64748b;">
                            <i class="fa-solid fa-user-secret fa-3x" style="display: block; margin-bottom: 1.5rem; opacity: 0.2;"></i>
                            No teacher records found.
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('teacherSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#teacherTable tbody tr.teacher-row');
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
        document.getElementById('teacherCount').textContent = visibleCount;
    });

    document.getElementById('teacherForm').onsubmit = function() {
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Finalizing...';
        btn.style.opacity = '0.8';
        btn.style.pointerEvents = 'none';
    };
</script>

<?php require_once 'includes/footer.php'; ?>
