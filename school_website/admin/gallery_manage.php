<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';
$message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_media') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $type = $_POST['type']; // 'image' or 'video'
    $media_url = '';
    
    // Handle File Upload
    if(isset($_FILES['media_file']) && $_FILES['media_file']['error'] == 0) {
        $upload_dir = '../images/gallery/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = time() . '_' . basename($_FILES['media_file']['name']);
        $target_file = $upload_dir . $filename;
        
        // Simple security check can be explicitly added here later for production
        if(move_uploaded_file($_FILES['media_file']['tmp_name'], $target_file)) {
            $media_url = 'images/gallery/' . $filename;
        } else {
             $message = "File upload failed.";
        }
    } elseif(isset($_POST['media_link']) && !empty($_POST['media_link'])) {
         // Allow external URLs (e.g., YouTube embed links)
         $media_url = $_POST['media_link'];
    }
    
    if($media_url !== '') {
        $stmt = $pdo->prepare("INSERT INTO gallery (title, category, type, media_url) VALUES (?, ?, ?, ?)");
        if($stmt->execute([$title, $category, $type, $media_url])) {
            $message = "Media uploaded successfully!";
        } else {
            $message = "Database error occurred.";
        }
    } else {
         $message = "Please provide an image/video file or external link.";
    }
}

// Handle Delete
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Option: Delete physical file as well
    $stmt = $pdo->prepare("SELECT media_url FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $media = $stmt->fetch();
    
    if($media && strpos($media['media_url'], 'images/gallery') === 0 && file_exists('../' . $media['media_url'])) {
        unlink('../' . $media['media_url']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: gallery_manage.php");
    exit;
}

// Fetch all media
$stmt = $pdo->query("SELECT * FROM gallery ORDER BY uploaded_at DESC");
$gallery_items = $stmt->fetchAll();
?>
<?php
$active_page = 'gallery';
$page_title = 'Gallery Management';
require_once 'includes/header.php';
?>

<style>
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2.5rem;
        margin-top: 3rem;
    }
    .media-card {
        background: var(--glass);
        backdrop-filter: var(--blur);
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid var(--glass-border);
        position: relative;
        transition: var(--transition);
        height: 320px;
    }
    .media-card:hover { border-color: var(--primary); transform: translateY(-8px); }
    .media-preview { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
    .media-card:hover .media-preview { transform: scale(1.1); }
    .media-info {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: linear-gradient(to top, rgba(15, 23, 42, 0.95), transparent);
        padding: 2.5rem 1.5rem 1.5rem;
        color: white; z-index: 2;
    }
    .badge-category {
        font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;
        background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(5px);
        padding: 0.4rem 0.8rem; border-radius: 50px; margin-bottom: 0.75rem; display: inline-block;
    }
    .btn-delete-asset {
        position: absolute; top: 1rem; right: 1rem; width: 40px; height: 40px;
        background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2);
        border-radius: 12px; display: flex; align-items: center; justify-content: center; z-index: 10;
        transition: var(--transition); text-decoration: none;
    }
    .btn-delete-asset:hover { background: #ef4444; color: white; transform: scale(1.1); }
    .filter-pills { display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap; }
    .pill {
        padding: 0.75rem 1.5rem; border-radius: 50px;
        background: rgba(255,255,255,0.03); color: #94a3b8; text-decoration: none;
        font-weight: 700; font-size: 0.85rem; border: 1px solid rgba(255,255,255,0.05);
        transition: var(--transition); cursor: pointer;
    }
    .pill.active { background: var(--primary); color: white; border-color: var(--primary); }
</style>

<?php if($message): ?>
    <div id="statusMessage" style="background: var(--secondary); color: white; padding: 1.25rem 2rem; border-radius: 16px; margin-bottom: 3rem; display: flex; align-items: center; justify-content: space-between; box-shadow: var(--shadow-lg); animation: slideDown 0.4s ease;">
        <div style="display: flex; align-items: center; gap: 1rem; font-weight: 700;">
            <i class="fa-solid fa-cloud-check fa-lg"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <i class="fa-solid fa-xmark cursor-pointer" onclick="this.parentElement.remove()"></i>
    </div>
<?php endif; ?>

<div class="card">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 3rem;">
        <div style="width: 55px; height: 55px; background: rgba(16, 185, 129, 0.08); color: var(--secondary); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
            <i class="fa-solid fa-camera-retro"></i>
        </div>
        <div>
            <h3 style="margin: 0; font-weight: 800; letter-spacing: -0.02em;">Ingest Media Assets</h3>
            <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.9rem;">Populate the institutional visual narrative.</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" id="uploadForm">
        <input type="hidden" name="action" value="add_media">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
            <div class="form-group">
                <label>Media Designation</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Convocation 2025 Highlights" required>
            </div>
            <div class="form-group">
                <label>Strategic Classification</label>
                <select name="category" class="form-control" required>
                    <option value="school_activities">Scholastic Operations</option>
                    <option value="sports">Athletic Valor</option>
                    <option value="events">Gala & Summits</option>
                    <option value="labs">Innovation Centers</option>
                    <option value="other">Miscellaneous</option>
                </select>
            </div>
            <div class="form-group">
                <label>Asset Format</label>
                <select name="type" id="mediaType" class="form-control" required>
                    <option value="image">Still Photography (Image)</option>
                    <option value="video">Motion Video (Video)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Direct Upload</label>
                <input type="file" name="media_file" id="media_file" class="form-control" accept="image/*,video/*" style="padding: 0.6rem;">
            </div>
        </div>
        
        <div style="margin: 2.5rem 0; display: flex; align-items: center; gap: 1.5rem;">
            <div style="flex: 1; height: 1px; background: var(--border);"></div>
            <span style="color: var(--text-muted); font-size: 0.75rem; font-weight: 800; letter-spacing: 0.1em;">DIGITAL BRIDGE</span>
            <div style="flex: 1; height: 1px; background: var(--border);"></div>
        </div>

        <div class="form-group">
            <label>Remote Resource URL (YouTube, Vimeo, etc.)</label>
            <input type="url" name="media_link" class="form-control" placeholder="https://youtube.com/v/...">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem; margin-top: 1rem;">
            <i class="fa-solid fa-upload"></i> Initialize Vault Entry
        </button>
    </form>
</div>

<div style="margin-top: 5rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h3 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em;">Verified Media Vault</h3>
            <div class="filter-pills">
                <div class="pill active" data-filter="all">Global Catalog</div>
                <div class="pill" data-filter="school_activities">Scholastic</div>
                <div class="pill" data-filter="sports">Athletics</div>
                <div class="pill" data-filter="events"> Summits</div>
                <div class="pill" data-filter="labs">Innovation</div>
            </div>
        </div>
        <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 700; background: white; padding: 0.75rem 1.5rem; border-radius: 50px; border: 1px solid var(--border);">
            <i class="fa-solid fa-cube" style="color: var(--primary);"></i> Vault Capacity: <?php echo count($gallery_items); ?> Units
        </div>
    </div>
</div>

<div class="media-grid">
    <?php foreach($gallery_items as $item): ?>
    <div class="media-card" data-category="<?php echo $item['category']; ?>">
        <?php if($item['type'] == 'image'): ?>
            <img class="media-preview" src="<?php echo strpos($item['media_url'], 'http') === 0 ? htmlspecialchars($item['media_url']) : '../'.htmlspecialchars($item['media_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
        <?php else: ?>
            <video class="media-preview" muted loop onmouseover="this.play()" onmouseout="this.pause()">
                <source src="<?php echo strpos($item['media_url'], 'http') === 0 ? htmlspecialchars($item['media_url']) : '../'.htmlspecialchars($item['media_url']); ?>">
            </video>
        <?php endif; ?>
        
        <div class="media-info">
            <span class="badge-category"><?php echo str_replace('_', ' ', $item['category']); ?></span>
            <h4 style="margin: 0; font-weight: 800; font-size: 1.15rem;"><?php echo htmlspecialchars($item['title']); ?></h4>
            <p style="font-size: 0.75rem; color: rgba(255,255,255,0.6); margin-top: 0.4rem; font-weight: 600;">ENCRYPTED STORAGE 0x<?php echo strtoupper(substr(md5($item['id']), 0, 8)); ?></p>
        </div>

        <a href="?delete=<?php echo $item['id']; ?>" class="btn-delete-asset" title="Secure Delete" onclick="return confirm('Initiate permanent removal of this asset from the institutional vault?');">
            <i class="fa-solid fa-shield-slash"></i>
        </a>
    </div>
    <?php endforeach; ?>
    
    <div id="noItemsMessage" style="display: none; grid-column: 1/-1; text-align: center; padding: 6rem; background: white; border-radius: 20px; border: 2px dashed var(--border);">
        <i class="fa-solid fa-scanner-gun fa-3x" style="color: #cbd5e1; margin-bottom: 1.5rem;"></i>
        <h3 style="color: var(--text-main); font-weight: 800;">No Catalog Matches</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Adjustment of classification filters required.</p>
    </div>

    <?php if(empty($gallery_items)): ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 6rem; background: white; border-radius: 20px; border: 2px dashed var(--border);">
            <i class="fa-solid fa-box-open fa-3x" style="color: #cbd5e1; margin-bottom: 1.5rem;"></i>
            <h3 style="color: var(--text-main); font-weight: 800;">Institutional Vault Empty</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Commence digital ingestion of scholastic memories.</p>
        </div>
    <?php endif; ?>
</div>

<script>
    document.querySelectorAll('.pill').forEach(pill => {
        pill.addEventListener('click', function() {
            document.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');
            const cards = document.querySelectorAll('.media-card');
            let visibleCount = 0;

            cards.forEach(card => {
                if(filter === 'all' || card.getAttribute('data-category') === filter) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('noItemsMessage').style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : 'none';
        });
    });

    document.getElementById('uploadForm').onsubmit = function() {
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Processing Asset...';
        btn.style.opacity = '0.8';
        btn.style.pointerEvents = 'none';
    };

    document.querySelector('input[name="media_link"]').addEventListener('input', function(e) {
        const val = e.target.value.toLowerCase();
        const typeSelect = document.getElementById('mediaType');
        if(val.includes('youtube') || val.includes('vimeo') || val.endsWith('.mp4')) {
            typeSelect.value = 'video';
        } else if(val.includes('unsplash') || val.endsWith('.jpg') || val.endsWith('.png')) {
            typeSelect.value = 'image';
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
