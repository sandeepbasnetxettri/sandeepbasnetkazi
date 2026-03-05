<?php 
require_once 'config/db.php';
include 'includes/header.php'; 

// Fetch gallery items from database
$stmt = $pdo->query("SELECT * FROM gallery ORDER BY uploaded_at DESC LIMIT 12");
$media_items = $stmt->fetchAll();
?>

<!-- Page Header -->
<div class="stats-section" style="padding: 6rem 0; text-align: center; border-bottom: 1px solid var(--border);">
    <div class="container reveal">
        <span style="color: var(--secondary); font-weight: 800; text-transform: uppercase; letter-spacing: 0.2em; font-size: 0.85rem; display: block; margin-bottom: 1rem;">Institutional Archive</span>
        <h1 style="font-family: var(--font-brand); font-size: clamp(2.5rem, 8vw, 4.5rem); font-weight: 800; color: var(--text-main); letter-spacing: -0.04em; margin-bottom: 1.5rem;">The Visual Narrative</h1>
        <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto; font-size: 1.1rem; line-height: 1.7;">Documenting milestones of academic excellence and institutional vibrancy.</p>
    </div>
</div>

<div class="container" style="padding: 6rem 1.5rem;">

    <!-- Filter Buttons Premium -->
    <div class="reveal" style="display: flex; justify-content: center; gap: 0.75rem; margin-bottom: 5rem; flex-wrap: wrap;">
        <button class="btn btn-primary" style="padding: 0.75rem 2rem; border-radius: 50px; font-weight: 700;">Complete Archive</button>
        <button class="btn" style="background: var(--glass); border: 1px solid var(--glass-border); color: var(--text-muted); padding: 0.75rem 2rem; border-radius: 50px; font-weight: 600;">Academic Wing</button>
        <button class="btn" style="background: var(--glass); border: 1px solid var(--glass-border); color: var(--text-muted); padding: 0.75rem 2rem; border-radius: 50px; font-weight: 600;">Athletic Highlights</button>
        <button class="btn" style="background: var(--glass); border: 1px solid var(--glass-border); color: var(--text-muted); padding: 0.75rem 2rem; border-radius: 50px; font-weight: 600;">Campus Culture</button>
    </div>

    <!-- Gallery Grid Premium -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
        
        <?php if(isset($media_items) && count($media_items) > 0): ?>
            <?php foreach($media_items as $item): ?>
            <div class="glass-card reveal" style="padding: 0; border-radius: 28px; overflow: hidden; position: relative; aspect-ratio: 4/5; cursor: pointer;">
                <?php if($item['type'] == 'image'): ?>
                    <img src="<?php echo htmlspecialchars($item['media_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);">
                <?php else: ?>
                    <video src="<?php echo htmlspecialchars($item['media_url']); ?>" preload="metadata" style="width: 100%; height: 100%; object-fit: cover;"></video>
                <?php endif; ?>
                <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(15, 23, 42, 0.95), transparent 60%); opacity: 0; transition: all 0.4s ease; display: flex; align-items: flex-end; padding: 2.5rem;" onmouseover="this.style.opacity='1'; this.previousElementSibling.style.transform='scale(1.1)'" onmouseout="this.style.opacity='0'; this.previousElementSibling.style.transform='scale(1)'">
                    <div>
                        <span style="color: var(--secondary); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; display: block; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($item['type']); ?></span>
                        <h4 style="color: white; font-family: var(--font-brand); font-size: 1.5rem; font-weight: 700; margin: 0;"><?php echo htmlspecialchars($item['title']); ?></h4>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Premium Placeholder Grid -->
            <?php 
            $placeholders = [
                ['url' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754', 'title' => 'Intellectual Exchange', 'cat' => 'Research'],
                ['url' => 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d', 'title' => 'Academic Procession', 'cat' => 'Ceremony'],
                ['url' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97', 'title' => 'Digital Architecture', 'cat' => 'Technology'],
                ['url' => 'https://images.unsplash.com/photo-1556910103-1c02745aae4d', 'title' => 'Culinary Laboratory', 'cat' => 'Arts'],
                ['url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950', 'title' => 'Competitive Spirit', 'cat' => 'Athletics'],
                ['url' => 'https://images.unsplash.com/photo-1523240715630-3663a8e79f64', 'title' => 'Collaborative Hub', 'cat' => 'Social']
            ];
            foreach($placeholders as $p): ?>
            <div class="glass-card reveal" style="padding: 0; border-radius: 28px; overflow: hidden; position: relative; aspect-ratio: 4/5; cursor: pointer;">
                <img src="<?php echo $p['url']; ?>?auto=format&fit=crop&w=800&q=80" alt="<?php echo $p['title']; ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);">
                <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(15, 23, 42, 0.95), transparent 70%); opacity: 0; transition: all 0.4s ease; display: flex; align-items: flex-end; padding: 2.5rem;" onmouseover="this.style.opacity='1'; this.previousElementSibling.style.transform='scale(1.1)'" onmouseout="this.style.opacity='0'; this.previousElementSibling.style.transform='scale(1)'">
                    <div>
                        <span style="color: var(--secondary); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; display: block; margin-bottom: 0.5rem;"><?php echo $p['cat']; ?></span>
                        <h4 style="color: white; font-family: var(--font-brand); font-size: 1.5rem; font-weight: 700; margin: 0;"><?php echo $p['title']; ?></h4>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<?php include 'includes/footer.php'; ?>
</div>