<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

$message = '';
// Handle Add Notice
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_notice') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $admin_id = $_SESSION['admin_id'];
    
    // Simple file handling for attachment
    $file_url = null;
    if(isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $upload_dir = '../images/uploads/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = time() . '_' . basename($_FILES['attachment']['name']);
        $target_file = $upload_dir . $filename;
        
        if(move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            $file_url = 'images/uploads/' . $filename; // Relative to root
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO notices (title, content, file_url, created_by) VALUES (?, ?, ?, ?)");
    if($stmt->execute([$title, $content, $file_url, $admin_id])) {
        $message = "Notice published successfully!";
    } else {
        $message = "Error publishing notice.";
    }
}

// Handle Delete Notice
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM notices WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: notices_manage.php");
    exit;
}

// Fetch all notices
$stmt = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC");
$notices = $stmt->fetchAll();
?>
<?php
$active_page = 'notices';
$page_title = 'Notice Board Management';
require_once 'includes/header.php';
?>

<?php if($message): ?>
    <div id="statusMessage" style="background: var(--secondary); color: white; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; animation: slideIn 0.5s ease; border: 1px solid rgba(255,255,255,0.1);">
        <i class="fa-solid fa-circle-check"></i>
        <?php echo $message; ?>
    </div>
    <script>
        setTimeout(() => {
            const msg = document.getElementById('statusMessage');
            if(msg) msg.style.opacity = '0';
            setTimeout(() => msg?.remove(), 500);
        }, 3000);
    </script>
<?php endif; ?>

<div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 3rem;">
    <!-- Form Section -->
    <div class="card">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2.5rem;">
            <div style="width: 45px; height: 45px; background: rgba(79, 70, 229, 0.1); color: #818cf8; border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(79, 70, 229, 0.2);">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <h3 style="margin: 0; font-weight: 800; color: white;">Publish New Notice</h3>
        </div>
        
        <form method="POST" enctype="multipart/form-data" id="noticeForm">
            <input type="hidden" name="action" value="add_notice">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Notice Title</label>
                <input type="text" name="title" id="noticeTitle" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; color: white; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.05)'" placeholder="E.g., Winter Vacation 2024" required>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Content Description</label>
                <textarea name="content" id="noticeContent" rows="5" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; color: white; outline: none; transition: 0.3s; resize: none;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='rgba(255,255,255,0.05)'" placeholder="Describe the notice details here..." required></textarea>
            </div>
            
            <div id="noticePreview" style="background: rgba(255, 255, 255, 0.02); padding: 1.5rem; border-radius: 12px; border-left: 4px solid var(--primary); margin-top: 1rem; display: none;">
                <small style="color: var(--primary); font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">Live Preview</small>
                <h4 id="prevTitle" style="margin: 0.75rem 0; color: white; font-weight: 700;"></h4>
                <p id="prevContent" style="font-size: 0.9rem; color: #94a3b8; white-space: pre-wrap; line-height: 1.6;"></p>
            </div>

            <div style="margin-top: 2rem; margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Attachment (PDF/Image)</label>
                <div style="border: 2px dashed rgba(255,255,255,0.1); padding: 2rem; border-radius: 12px; text-align: center; cursor: pointer; transition: 0.3s;" onmouseover="this.style.borderColor='var(--primary)'; this.style.background='rgba(79,70,229,0.05)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.background='transparent'" onclick="document.getElementById('fileInput').click()">
                    <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2rem; color: #4f46e5; margin-bottom: 1rem;"></i>
                    <p style="font-size: 0.9rem; color: #94a3b8; font-weight: 500;" id="fileName">Click or Drag to Upload</p>
                    <input type="file" name="attachment" id="fileInput" hidden onchange="document.getElementById('fileName').textContent = this.files[0].name; document.getElementById('fileName').style.color='white'">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem;">
                <i class="fa-solid fa-paper-plane"></i> Publish Notice Board
            </button>
        </form>
    </div>

    <!-- List Section -->
    <div class="card" style="flex: 1.5;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2.5rem;">
            <div style="width: 45px; height: 45px; background: rgba(16, 185, 129, 0.1); color: var(--secondary); border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(16, 185, 129, 0.2);">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <h3 style="margin: 0; font-weight: 800; color: white;">Published Notices</h3>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Notice Details</th>
                        <th style="text-align: center;">Media</th>
                        <th style="text-align: center;">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($notices as $n): ?>
                    <tr>
                        <td style="white-space: nowrap;">
                            <div style="font-weight: 700; color: white;"><?php echo date('M d', strtotime($n['created_at'])); ?></div>
                            <small style="color: #64748b; font-weight: 600;"><?php echo date('Y', strtotime($n['created_at'])); ?></small>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: white; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($n['title']); ?></div>
                            <small style="color: #94a3b8; display: block; max-width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.85rem;">
                                <?php echo htmlspecialchars($n['content']); ?>
                            </small>
                        </td>
                        <td style="text-align: center;">
                            <?php if($n['file_url']): ?>
                                <a href="../<?php echo $n['file_url']; ?>" target="_blank" style="color: var(--secondary); font-size: 1.25rem; transition: 0.3s;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>
                            <?php else: ?>
                                <span style="color: rgba(255,255,255,0.05);">-</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <a href="?delete=<?php echo $n['id']; ?>" style="color: #ef4444; font-size: 1.1rem; padding: 0.5rem; transition: 0.3s;" onmouseover="this.style.color='#f87171'" onmouseout="this.style.color='#ef4444'" onclick="return confirm('Archive this notice?');">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($notices)): ?>
                        <tr><td colspan="4" style="text-align: center; padding: 5rem; color: #64748b;">
                            <i class="fa-solid fa-box-open fa-3x" style="display: block; margin-bottom: 1.5rem; opacity: 0.3;"></i>
                            No notices published yet.
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const titleInp = document.getElementById('noticeTitle');
    const contentInp = document.getElementById('noticeContent');
    const previewBox = document.getElementById('noticePreview');
    const prevTitle = document.getElementById('prevTitle');
    const prevContent = document.getElementById('prevContent');

    function updatePreview() {
        if(titleInp.value || contentInp.value) {
            previewBox.style.display = 'block';
            prevTitle.textContent = titleInp.value;
            prevContent.textContent = contentInp.value;
        } else {
            previewBox.style.display = 'none';
        }
    }

    titleInp.addEventListener('input', updatePreview);
    contentInp.addEventListener('input', updatePreview);

    // Form Animation
    document.getElementById('noticeForm').onsubmit = function() {
        const btn = this.querySelector('.btn-publish');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Publishing...';
        btn.style.opacity = '0.7';
    };
</script>

<?php require_once 'includes/footer.php'; ?>
