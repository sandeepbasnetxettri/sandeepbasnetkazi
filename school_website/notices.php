<?php 
require_once 'config/db.php';
include 'includes/header.php'; 

// Fetch notices from database
$stmt = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC LIMIT 10");
$notices = $stmt->fetchAll();
?>

<!-- Page Header -->
<div class="stats-section" style="padding: 6rem 0; text-align: center; border-bottom: 1px solid var(--border);">
    <div class="container reveal">
        <span style="color: var(--secondary); font-weight: 800; text-transform: uppercase; letter-spacing: 0.25em; font-size: 0.85rem; display: block; margin-bottom: 1rem;">Information Infrastructure</span>
        <h1 style="font-family: var(--font-brand); font-size: clamp(2.5rem, 8vw, 4.5rem); font-weight: 800; color: var(--text-main); letter-spacing: -0.04em; margin-bottom: 1.5rem;">Institutional Notices</h1>
        <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto; font-size: 1.1rem; line-height: 1.7;">Official directives, pedagogical updates, and campus narratives.</p>
    </div>
</div>

<div class="container reveal" style="padding: 6rem 1.5rem;">
    <!-- Main Notices Area -->
    <div class="feature-grid" style="gap: 4rem; align-items: start;">
        <div style="grid-column: span 1;">
            <div style="display: flex; align-items: center; gap: 1.25rem; margin-bottom: 4rem;">
                <div style="width: 60px; height: 60px; background: rgba(79, 70, 229, 0.1); color: #818cf8; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; flex-shrink: 0; border: 1px solid rgba(79, 70, 229, 0.2);">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div>
                    <h2 style="margin: 0; font-family: var(--font-brand); font-size: 2rem; font-weight: 800; color: var(--text-main);">Global Announcements</h2>
                    <p style="margin: 0; color: var(--text-muted); font-size: 1rem;">Primary communication vectors.</p>
                </div>
            </div>

            <?php if(isset($notices) && count($notices) > 0): ?>
                <div style="display: flex; flex-direction: column; gap: 2.5rem;">
                    <?php foreach($notices as $notice): ?>
                    <div class="glass-card reveal" style="padding: 3rem; position: relative;">
                        <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--primary); border-radius: 2px;"></div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1.5rem;">
                            <h3 style="color: var(--text-main); font-family: var(--font-brand); font-size: 1.75rem; font-weight: 700; margin: 0; letter-spacing: -0.02em;"><?php echo htmlspecialchars($notice['title']); ?></h3>
                            <div style="font-size: 0.85rem; color: #818cf8; background: rgba(129, 140, 248, 0.1); padding: 0.6rem 1.2rem; border-radius: 50px; font-weight: 800; border: 1px solid rgba(129, 140, 248, 0.2); white-space: nowrap;">
                                <i class="fa-regular fa-clock" style="margin-right: 0.5rem;"></i> <?php echo date('M d, Y', strtotime($notice['created_at'])); ?>
                            </div>
                        </div>
                        <p style="color: var(--text-muted); margin-bottom: 2.5rem; line-height: 1.8; font-size: 1.05rem;">
                            <?php echo nl2br(htmlspecialchars($notice['content'])); ?>
                        </p>
                        <?php if($notice['file_url']): ?>
                        <div style="padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.05);">
                            <a href="<?php echo htmlspecialchars($notice['file_url']); ?>" target="_blank" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
                                <i class="fa-solid fa-file-pdf" style="margin-right: 0.75rem;"></i> Visualize Technical Document
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="glass-card reveal" style="padding: 6rem 2rem; text-align: center; border: 2px dashed var(--border);">
                    <i class="fa-solid fa-satellite-dish fa-4x" style="color: var(--border); margin-bottom: 2rem;"></i>
                    <h3 style="color: var(--text-muted); font-family: var(--font-brand); font-weight: 700; font-size: 1.5rem;">No Active Broadcasts</h3>
                    <p style="color: var(--text-muted);">System is currently in a silent operational state.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Premium -->
        <div class="sidebar-sticky">
            <div class="glass-card" style="padding: 2.5rem; border-color: rgba(16, 185, 129, 0.1);">
                <h3 style="color: var(--text-main); font-family: var(--font-brand); font-weight: 800; display: flex; align-items: center; gap: 0.75rem; margin-bottom: 2.5rem; font-size: 1.4rem;">
                    <i class="fa-solid fa-calendar-day" style="color: #10b981;"></i> Periodic Landmarks
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    <div style="display: flex; gap: 1.25rem;">
                        <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.75rem; border-radius: 16px; text-align: center; min-width: 65px; height: 65px; display: flex; flex-direction: column; justify-content: center; border: 1px solid rgba(16, 185, 129, 0.2);">
                            <span style="font-size: 1.5rem; font-weight: 900; display: block; line-height: 1;">25</span>
                            <span style="font-size: 0.7rem; text-transform: uppercase; font-weight: 800;">Nov</span>
                        </div>
                        <div style="display: flex; flex-direction: column; justify-content: center;">
                            <h4 style="color: var(--text-main); font-weight: 700; margin-bottom: 0.25rem; font-size: 1.1rem; line-height: 1.2;">Symposium 2024</h4>
                            <p style="color: var(--text-muted); font-size: 0.85rem;"><i class="fa-solid fa-location-dot" style="margin-right: 0.3rem;"></i> Grand Arena</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1.25rem;">
                        <div style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 0.75rem; border-radius: 16px; text-align: center; min-width: 65px; height: 65px; display: flex; flex-direction: column; justify-content: center; border: 1px solid rgba(245, 158, 11, 0.2);">
                            <span style="font-size: 1.5rem; font-weight: 900; display: block; line-height: 1;">12</span>
                            <span style="font-size: 0.7rem; text-transform: uppercase; font-weight: 800;">Dec</span>
                        </div>
                        <div style="display: flex; flex-direction: column; justify-content: center;">
                            <h4 style="color: white; font-weight: 700; margin-bottom: 0.25rem; font-size: 1.1rem; line-height: 1.2;">Pedagogy Expo</h4>
                            <p style="color: #64748b; font-size: 0.85rem;"><i class="fa-solid fa-location-dot" style="margin-right: 0.3rem;"></i> Tech Center</p>
                        </div>
                    </div>
                </div>

                <a href="#" class="btn" style="width: 100%; margin-top: 3rem; justify-content: center; background: var(--glass); border: 1px solid var(--glass-border); color: var(--text-main); padding: 1rem; border-radius: 14px; font-weight: 700;">
                    Complete Academic Roadmap
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@media (min-width: 992px) {
    .sidebar-sticky { position: sticky; top: 120px; }
}
</style>

<?php include 'includes/footer.php'; ?>
