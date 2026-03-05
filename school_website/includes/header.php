<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Load Google Analytics configuration
require_once 'config/analytics.php';

// Set analytics debug mode if needed
if (defined('ANALYTICS_DEBUG_MODE') && ANALYTICS_DEBUG_MODE) {
    // Debug mode enabled - add console logs
    echo '<script>console.log("Analytics Debug Mode Enabled");</script>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Everest International School | Excellence in Education</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo GOOGLE_ANALYTICS_ID; ?>"></script>
    <script>
        window.GOOGLE_ANALYTICS_ID = '<?php echo GOOGLE_ANALYTICS_ID; ?>';
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo GOOGLE_ANALYTICS_ID; ?>');
        
        // Immediate theme application to prevent flash
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar">
    <div class="container nav-container">
        <a href="index.php" class="brand">
            <div class="logo-box" style="width: 48px; height: 48px; background: linear-gradient(135deg, #4f46e5, #6366f1); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white;">
                <i class="fa-solid fa-graduation-cap fa-lg"></i>
            </div>
            <div class="brand-text">
                <h1 style="font-family: var(--font-brand); font-weight: 800; letter-spacing: -0.04em; font-size: 1.75rem; color: var(--text-main); line-height: 1; transition: var(--transition);">EVEREST</h1>
                <p style="text-transform: uppercase; letter-spacing: 0.15em; font-size: 0.65rem; color: #818cf8; font-weight: 700;">Institutional Excellence</p>
            </div>
        </a>
        
        <ul class="nav-links">
            <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a></li>
            <li><a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About</a></li>
            <li><a href="academics.php" class="<?php echo ($current_page == 'academics.php') ? 'active' : ''; ?>">Academics</a></li>
            <li><a href="admission.php" class="<?php echo ($current_page == 'admission.php') ? 'active' : ''; ?>">Admission</a></li>
            <li><a href="notices.php" class="<?php echo ($current_page == 'notices.php') ? 'active' : ''; ?>">Notices</a></li>
            <li><a href="gallery.php" class="<?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>">Gallery</a></li>
            <li><a href="contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
        </ul>
        
        <div class="nav-actions">
            <button id="theme-toggle" class="btn-theme" style="background: var(--glass); border: 1px solid var(--glass-border); color: var(--text-muted); width: 45px; height: 45px; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.3s;">
                <i class="fa-solid fa-moon"></i>
            </button>
            <a href="login.php" class="btn btn-primary" style="padding: 0.75rem 1.75rem; border-radius: 50px; font-weight: 700; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.75rem;">
                <i class="fa-solid fa-shield-halved"></i> COMMAND CENTER
            </a>
            <div class="mobile-toggle">
                <i class="fa-solid fa-bars-staggered" style="font-size: 1.5rem; color: var(--text-main);"></i>
            </div>
        </div>
    </div>
</nav>
