<?php
session_start();
require_once '../config/db.php';

$active_page = 'settings';
$page_title = 'System Settings';
$message = '';

// Handle Update Settings
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_settings') {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    $message = "Settings updated successfully!";
}

// Fetch all settings
$stmt = $pdo->query("SELECT * FROM settings");
$results = $stmt->fetchAll();
$settings = [];
foreach ($results as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

require_once 'includes/header.php';
?>

<style>
    .settings-card { border: none; max-width: 900px; margin: 0 auto; }
</style>

<?php if ($message): ?>
    <div id="statusMessage" style="background: var(--secondary); color: white; padding: 1.25rem 2rem; border-radius: 12px; margin-bottom: 3rem; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(255,255,255,0.1); animation: slideIn 0.4s ease; max-width: 900px; margin: 0 auto 3rem;">
        <div style="display: flex; align-items: center; gap: 1rem; font-weight: 700;">
            <i class="fa-solid fa-sliders"></i>
            <?php echo $message; ?>
        </div>
        <i class="fa-solid fa-xmark cursor-pointer" onclick="this.parentElement.remove()"></i>
    </div>
<?php endif; ?>

<div class="card settings-card">
    <div style="text-align: center; margin-bottom: 4rem;">
        <div style="width: 70px; height: 70px; background: rgba(79, 70, 229, 0.1); color: #818cf8; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem; border: 1px solid rgba(79, 70, 229, 0.2);">
            <i class="fa-solid fa-microchip"></i>
        </div>
        <h2 style="font-size: 2.25rem; font-weight: 900; color: white; letter-spacing: -0.04em; margin-bottom: 0.75rem;">System Infrastructure</h2>
        <p style="color: #94a3b8; font-size: 1rem; max-width: 500px; margin: 0 auto;">Configure institutional global variables and digital twin parameters.</p>
    </div>

    <form method="POST" id="settingsForm">
        <input type="hidden" name="action" value="update_settings">
        
        <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 1.15rem; font-weight: 800; color: white; margin-bottom: 2rem;">
            <div style="width: 4px; height: 18px; background: var(--secondary); border-radius: 2px;"></div>
            Institutional Identity
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Institutional Designation (Formal Name)</label>
                <input type="text" name="settings[school_name]" value="<?php echo htmlspecialchars($settings['school_name'] ?? ''); ?>" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1.1rem; border-radius: 12px; color: white; outline: none;" placeholder="Institutional Name">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Official Communication Node (Email)</label>
                <input type="email" name="settings[school_email]" value="<?php echo htmlspecialchars($settings['school_email'] ?? ''); ?>" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1.1rem; border-radius: 12px; color: white; outline: none;" placeholder="contact@everest.edu">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Direct Liaison Hotline (Phone)</label>
                <input type="text" name="settings[school_phone]" value="<?php echo htmlspecialchars($settings['school_phone'] ?? ''); ?>" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1.1rem; border-radius: 12px; color: white; outline: none;" placeholder="+977-XXXXXXXXXX">
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label style="display: block; margin-bottom: 0.75rem; font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Primary Geolocation / Physical Address</label>
                <input type="text" name="settings[school_address]" value="<?php echo htmlspecialchars($settings['school_address'] ?? ''); ?>" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 1.1rem; border-radius: 12px; color: white; outline: none;" placeholder="HQ Address">
            </div>
        </div>
        
        <div style="padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="padding: 1.25rem 3rem;">
                <i class="fa-solid fa-shield-check"></i> Standardize Global Settings
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('settingsForm').onsubmit = function() {
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Writing to Infrastructure...';
        btn.style.opacity = '0.8';
        btn.style.pointerEvents = 'none';
    };
</script>

<?php require_once 'includes/footer.php'; ?>
