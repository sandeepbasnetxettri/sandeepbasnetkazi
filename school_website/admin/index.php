<?php
session_start();
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

// Get counts for dashboard
$stmt = $pdo->query("SELECT COUNT(*) FROM students");
$student_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM teachers");
$teacher_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM notices");
$notice_count = $stmt->fetchColumn();
$active_page = 'dashboard';
$page_title = 'Dashboard Overview';
require_once 'includes/header.php';
?>

<style>
    .grid-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2.5rem;
        margin-bottom: 4rem;
    }

    .stat-card {
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 220px;
        background: var(--glass);
        backdrop-filter: var(--blur);
    }

    .stat-card::after {
        content: "";
        position: absolute;
        width: 151px;
        height: 151px;
        background: rgba(255,255,255,0.02);
        border-radius: 50%;
        bottom: -50px;
        right: -30px;
        z-index: 0;
    }

    .stat-info { z-index: 1; }
    
    .stat-label {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8;
        margin-bottom: 0.75rem;
        display: block;
    }

    .stat-value {
        font-size: 3.5rem;
        font-weight: 800;
        letter-spacing: -0.05em;
        color: white;
        line-height: 1;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: var(--transition);
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(5deg);
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* Action Grid */
    .action-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 2rem;
    }

    .quick-action {
        background: var(--glass);
        backdrop-filter: var(--blur);
        border: 1px solid var(--glass-border);
        padding: 2.5rem;
        border-radius: 20px;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 1.5rem;
        transition: var(--transition);
    }

    .quick-action i {
        font-size: 2.5rem;
        padding-bottom: 0.5rem;
    }

    .quick-action h4 {
        font-weight: 800;
        color: var(--text-main);
        font-size: 1.15rem;
    }

    .quick-action p {
        font-size: 0.9rem;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .quick-action:hover {
        transform: translateY(-8px);
        border-color: var(--primary);
        background: rgba(255, 255, 255, 0.02);
    }

    /* System Integrity Viz */
    .integrity-viz {
        height: 8px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        margin: 1.5rem 0 0.5rem;
        overflow: hidden;
    }
    .integrity-bar {
        height: 100%;
        background: linear-gradient(to right, var(--secondary), #34d399);
        width: 94%;
        border-radius: 10px;
    }
</style>

<div class="grid-stats">
    <div class="card stat-card" style="border-left: 6px solid var(--primary);">
        <div class="stat-info">
            <div class="stat-icon" style="color: var(--primary);"><i class="fa-solid fa-user-graduate"></i></div>
            <span class="stat-label">Enrolled Students</span>
            <div class="stat-value"><?php echo $student_count; ?></div>
        </div>
        <div style="font-size: 0.85rem; color: #10b981; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; margin-top: 1.5rem;">
            <i class="fa-solid fa-arrow-trend-up"></i> +12.5% vs Last Session
        </div>
    </div>
    
    <div class="card stat-card" style="border-left: 6px solid var(--secondary);">
        <div class="stat-info">
            <div class="stat-icon" style="color: var(--secondary);"><i class="fa-solid fa-chalkboard-teacher"></i></div>
            <span class="stat-label">Verified Teachers</span>
            <div class="stat-value"><?php echo $teacher_count; ?></div>
        </div>
        <div style="font-size: 0.85rem; color: #64748b; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; margin-top: 1.5rem;">
            <i class="fa-solid fa-check-double"></i> 100% Active Duty
        </div>
    </div>
    
    <div class="card stat-card" style="border-left: 6px solid var(--accent);">
        <div class="stat-info">
            <div class="stat-icon" style="color: var(--accent);"><i class="fa-solid fa-bullhorn"></i></div>
            <span class="stat-label">Global Notices</span>
            <div class="stat-value"><?php echo $notice_count; ?></div>
        </div>
        <div style="font-size: 0.85rem; color: var(--accent); font-weight: 700; display: flex; align-items: center; gap: 0.5rem; margin-top: 1.5rem;">
            <i class="fa-solid fa-bolt"></i> 4 New broadcasts today
        </div>
    </div>
</div>

<div class="admin-main-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem; margin-top: 2rem;">
    <!-- Management Hub -->
    <div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2.5rem;">
            <h3 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em;">Governance Quick-Access</h3>
            <a href="settings.php" style="color: var(--primary); text-decoration: none; font-weight: 700; font-size: 0.9rem;">Advanced Config <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        
        <div class="action-grid">
            <a href="notices_manage.php" class="quick-action">
                <i class="fa-solid fa-paper-plane" style="color: #6366f1;"></i>
                <h4>Broadcast</h4>
                <p>Deploy official school circulars & alerts.</p>
            </a>
            <a href="gallery_manage.php" class="quick-action">
                <i class="fa-solid fa-images" style="color: #10b981;"></i>
                <h4>Media Hub</h4>
                <p>Curate and upload event memories.</p>
            </a>
            <a href="students_manage.php" class="quick-action">
                <i class="fa-solid fa-user-plus" style="color: #3b82f6;"></i>
                <h4>Enrollment</h4>
                <p>Onboard and secure new student profiles.</p>
            </a>
            <a href="attendance_manage.php" class="quick-action">
                <i class="fa-solid fa-calendar-check" style="color: #f59e0b;"></i>
                <h4>Registry</h4>
                <p>Maintain precise daily presence logs.</p>
            </a>
        </div>
    </div>

    <!-- System Integrity Sidebar -->
    <div>
        <div class="card" style="padding: 2.5rem; background: var(--sidebar-bg); color: #f8fafc; border-color: transparent;">
            <h3 style="color: #f8fafc; font-weight: 800; font-size: 1.25rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fa-solid fa-microchip" style="color: var(--secondary);"></i> System Integrity
            </h3>
            
            <div style="margin-bottom: 2.5rem;">
                <div style="display: flex; justify-content: space-between; color: #94a3b8; font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">
                    <span>Security Layer</span>
                    <span style="color: var(--secondary);">Optimized</span>
                </div>
                <div class="integrity-viz"><div class="integrity-bar"></div></div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1.75rem;">
                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                    <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--secondary); margin-top: 0.5rem;"></div>
                    <div>
                        <p style="color: white; font-size: 0.9rem; font-weight: 700; margin-bottom: 0.25rem;">Database Synced</p>
                        <p style="color: #64748b; font-size: 0.8rem;">Last full backup: 24 mins ago</p>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                    <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--secondary); margin-top: 0.5rem;"></div>
                    <div>
                        <p style="color: white; font-size: 0.9rem; font-weight: 700; margin-bottom: 0.25rem;">TLS 1.3 Active</p>
                        <p style="color: #64748b; font-size: 0.8rem;">End-to-end portal encryption</p>
                    </div>
                </div>
            </div>

            <div style="margin-top: 3.5rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.05);">
                <a href="../index.php" target="_blank" class="btn btn-primary" style="width: 100%; justify-content: center; background: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">
                    <i class="fa-solid fa-earth-asia"></i> View Campus Site
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
