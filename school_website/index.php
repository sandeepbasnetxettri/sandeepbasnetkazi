<?php 
require_once 'config/db.php';
include 'includes/header.php'; 

// Fetch Counts
$stmt = $pdo->query("SELECT COUNT(*) FROM students");
$student_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM teachers");
$teacher_count = $stmt->fetchColumn();

// Fetch Latest 3 Notices
$stmt = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC LIMIT 3");
$latest_notices = $stmt->fetchAll();
?>

<!-- Hero Slider Section -->
<section class="hero">
    <div class="hero-slider">
        <!-- Slide 1 -->
        <div class="slide">
            <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Students in campus" loading="lazy">
            <div class="slide-content">
                <h2 class="reveal">Welcome to Everest School</h2>
                <p class="reveal">Nurturing minds, shaping futures with excellence and a commitment to global standards.</p>
                <div class="reveal">
                    <a href="about.php" class="btn btn-primary">Discover Our Legacy</a>
                </div>
            </div>
        </div>
        <!-- Slide 2: Events -->
        <div class="slide">
            <img src="https://images.unsplash.com/photo-1546410531-bb4caa6b424d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Graduation Event" loading="lazy">
            <div class="slide-content">
                <h2>Annual Sports Meet 2026</h2>
                <p>Join us in celebrating athleticism, teamwork, and the spirit of competition.</p>
                <a href="notices.php" class="btn btn-secondary">Upcoming Events</a>
            </div>
        </div>
        <!-- Slide 3: Achievements -->
        <div class="slide">
            <img src="https://images.unsplash.com/photo-1588667355001-eb2c1e29c077?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Laboratory" loading="lazy">
            <div class="slide-content">
                <h2>State-of-the-Art Learning</h2>
                <p>Equipping students with modern tools in Computer Science and Hotel Management.</p>
                <a href="academics.php" class="btn btn-primary">Explore Programs</a>
            </div>
        </div>
    </div>
    
    <div class="slider-controls">
        <div class="slider-dot active" onclick="currentSlide(0)"></div>
        <div class="slider-dot" onclick="currentSlide(1)"></div>
        <div class="slider-dot" onclick="currentSlide(2)"></div>
    </div>
</section>

<!-- Quick Links Grid -->
<div class="container relative z-10">
    <div class="quick-links reveal">
        <div class="quick-link-card">
            <a href="admission.php">
                <i class="fa-solid fa-graduation-cap"></i>
                <h3>Admissions 2026</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem;">Join the session</p>
            </a>
        </div>
        <div class="quick-link-card">
            <a href="results.php">
                <i class="fa-solid fa-chart-line"></i>
                <h3>Exam Results</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem;">Academic progress</p>
            </a>
        </div>
        <div class="quick-link-card">
            <a href="notices.php">
                <i class="fa-solid fa-bell"></i>
                <h3>Latest Notices</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem;">Stay updated</p>
            </a>
        </div>
        <div class="quick-link-card">
            <a href="contact.php">
                <i class="fa-solid fa-headset"></i>
                <h3>Support Center</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem;">We are here to help</p>
            </a>
        </div>
    </div>
</div>

<!-- Principal Message -->
<section>
    <div class="container feature-grid">
        <div class="reveal">
            <span class="sub-heading">Message from Leadership</span>
            <h2 class="hero-title">Guiding the Next Generation</h2>
            <p class="hero-desc">
                "At Everest School, we believe that education is not just about academic excellence, but about character building and innovation. Our mission is to provide a vibrant environment where curiosity is nurtured."
            </p>
            <div class="principal-meta">
                <div class="principal-img">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Principal">
                </div>
                <div>
                   <h4>Dr. Sharma</h4>
                   <p>Principal, Everest School</p>
                </div>
            </div>
            <a href="about.php" class="btn btn-primary">Read Biography</a>
        </div>
        <div class="reveal principal-visual">
            <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Students studying">
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container stats-grid">
        <div class="stat-item reveal">
            <i class="fa-solid fa-user-graduate"></i>
            <h3><span class="counter" data-target="<?php echo $student_count; ?>">0</span>+</h3>
            <p>Active Students</p>
        </div>
        <div class="stat-item reveal">
            <i class="fa-solid fa-chalkboard-teacher"></i>
            <h3><span class="counter" data-target="<?php echo $teacher_count; ?>">0</span>+</h3>
            <p>Expert Teachers</p>
        </div>
        <div class="stat-item reveal">
            <i class="fa-solid fa-book-open"></i>
            <h3><span class="counter" data-target="25">0</span>+</h3>
            <p>Diverse Courses</p>
        </div>
        <div class="stat-item reveal">
            <i class="fa-solid fa-trophy"></i>
            <h3><span class="counter" data-target="20">0</span>+</h3>
            <p>Years of Legacy</p>
        </div>
    </div>
</section>

<!-- Dynamic Latest Notices Section -->
<?php if (!empty($latest_notices)): ?>
<section style="background: var(--glass); padding: 5rem 0; border-top: 1px solid var(--glass-border);">
    <div class="container">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 3rem;" class="reveal">
            <div>
                <span class="sub-heading" style="color: var(--secondary);">Stay Informed</span>
                <h2 style="font-size: 2.5rem; font-weight: 800; color: var(--text-main); margin-top: 0.5rem;">Latest Bulletins</h2>
            </div>
            <a href="notices.php" class="btn btn-secondary btn-sm">View All Archives <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <?php foreach ($latest_notices as $notice): ?>
            <div class="glass-card reveal" style="padding: 2.5rem; border-top: 4px solid var(--primary);">
                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; display: block; margin-bottom: 1rem;">
                    <i class="fa-regular fa-clock"></i> <?php echo date('M d, Y', strtotime($notice['created_at'])); ?>
                </span>
                <h3 style="font-weight: 800; color: var(--text-main); margin-bottom: 1.25rem; line-height: 1.3;"><?php echo htmlspecialchars($notice['title']); ?></h3>
                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem;">
                    <?php echo substr(strip_tags($notice['content']), 0, 120) . '...'; ?>
                </p>
                <a href="notices.php" style="color: var(--primary); font-weight: 700; text-decoration: none; font-size: 0.9rem;">Read Full Protocol <i class="fa-solid fa-angle-right"></i></a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
