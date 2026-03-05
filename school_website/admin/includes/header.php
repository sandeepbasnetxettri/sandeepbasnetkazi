<?php
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | Everest Governance</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --secondary: #10b981;
            --accent: #f59e0b;
            --sidebar-bg: #0f172a;
            --radius: 12px;
            --radius-lg: 20px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-theme="dark"] {
            --bg-color: #020617;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(255, 255, 255, 0.05);
            --glass: rgba(15, 23, 42, 0.7);
            --glass-border: rgba(255, 255, 255, 0.05);
            --blur: blur(12px);
            --card-bg: rgba(255, 255, 255, 0.02);
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        [data-theme="light"] {
            --bg-color: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: rgba(0, 0, 0, 0.05);
            --glass: rgba(255, 255, 255, 0.8);
            --glass-border: rgba(0, 0, 0, 0.05);
            --blur: blur(12px);
            --card-bg: #ffffff;
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-color); 
            color: var(--text-main); 
            display: flex;
            min-height: 100vh;
        }

        /* Premium Sidebar */
        .sidebar {
            width: 280px;
            background: #0f172a;
            color: #f8fafc;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            padding: 2rem 1.25rem;
            z-index: 1000;
            border-right: 1px solid rgba(255,255,255,0.05);
            transition: var(--transition);
        }

        .sidebar-brand { 
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0 0.75rem 2.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 2rem;
        }

        .sidebar-brand i {
            font-size: 2rem;
            color: var(--secondary);
        }

        .sidebar-brand h2 {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: white;
        }

        .nav-menu {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #94a3b8;
            text-decoration: none;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .nav-link i { 
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: white;
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }

        /* Main Content Area */
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 2.5rem 3.5rem;
            max-width: calc(100% - 280px);
        }

        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3.5rem;
            background: var(--glass);
            backdrop-filter: var(--blur);
            padding: 1.25rem 2.5rem;
            border-radius: 20px;
            border: 1px solid var(--glass-border);
        }

        .page-info h1 {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text-main);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .admin-badge {
            background: rgba(79, 70, 229, 0.1);
            color: #818cf8;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid rgba(129, 140, 248, 0.2);
        }

        .btn-logout {
            background: #fef2f2;
            color: #ef4444;
            padding: 0.6rem 1.25rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            transition: var(--transition);
        }

        .btn-logout:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-2px);
        }

        /* Components Modernized */
        .card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 2rem;
            transition: var(--transition);
        }

        [data-theme="light"] .table, [data-theme="light"] .table th { background: white; }

        .card:hover { border-color: var(--primary); }

        .btn {
            padding: 0.85rem 1.75rem;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); }

        /* Tables Premium */
        .table-container {
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .table th {
            background: var(--card-bg);
            padding: 1.25rem 1.5rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            border-bottom: 2px solid var(--border);
        }

        .table td {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
            font-size: 0.95rem;
        }

        @media (max-width: 1200px) {
            .sidebar { width: 80px; padding: 2rem 0.75rem; }
            .sidebar-brand h2, .nav-link span { display: none; }
            .nav-link { justify-content: center; padding: 1.25rem; }
            .main-content { margin-left: 80px; max-width: calc(100% - 80px); padding: 2rem; }
        }

        @media (max-width: 768px) {
            .sidebar { 
                position: fixed; 
                left: -280px; 
                width: 280px; 
                height: 100vh; 
                transition: var(--transition);
                z-index: 2000;
                display: flex;
            }
            .sidebar.active { left: 0; }
            .sidebar-brand h2, .nav-link span { display: block; }
            .nav-link { justify-content: flex-start; padding: 1rem 1.25rem; }
            
            .main-content { margin-left: 0; max-width: 100%; padding: 1rem; }
            .top-nav { padding: 1rem; margin-bottom: 2rem; border-radius: 12px; }
            .mobile-admin-toggle { display: flex !important; align-items: center; justify-content: center; width: 45px; height: 45px; background: #f1f5f9; border-radius: 10px; cursor: pointer;}
            
            .stat-card { padding: 1.5rem; min-height: auto; }
            .stat-value { font-size: 2.25rem; }
            .admin-main-grid { gap: 1.5rem; }
        }

        .theme-toggle-admin {
            width: 45px;
            height: 45px;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }
        .theme-toggle-admin:hover { transform: translateY(-2px); border-color: var(--primary); }
    </style>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body data-theme="dark">

<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-graduation-cap"></i>
        <h2>Everest <span>Governance</span></h2>
    </div>
    
    <nav class="nav-menu">
        <a href="index.php" class="nav-link <?php echo ($active_page == 'dashboard') ? 'active' : ''; ?>">
            <i class="fa-solid fa-grid-2"></i> <span>Dashboard</span>
        </a>
        <a href="notices_manage.php" class="nav-link <?php echo ($active_page == 'notices') ? 'active' : ''; ?>">
            <i class="fa-solid fa-bullhorn"></i> <span>Notices</span>
        </a>
        <a href="gallery_manage.php" class="nav-link <?php echo ($active_page == 'gallery') ? 'active' : ''; ?>">
            <i class="fa-solid fa-images"></i> <span>Media Gallery</span>
        </a>
        <a href="attendance_manage.php" class="nav-link <?php echo ($active_page == 'attendance') ? 'active' : ''; ?>">
            <i class="fa-solid fa-calendar-check"></i> <span>Attendance</span>
        </a>
        <a href="results_manage.php" class="nav-link <?php echo ($active_page == 'results') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-column"></i> <span>Examinations</span>
        </a>
        <a href="students_manage.php" class="nav-link <?php echo ($active_page == 'students') ? 'active' : ''; ?>">
            <i class="fa-solid fa-user-graduate"></i> <span>Students</span>
        </a>
        <a href="teachers_manage.php" class="nav-link <?php echo ($active_page == 'teachers') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chalkboard-teacher"></i> <span>Teachers</span>
        </a>
        <a href="settings.php" class="nav-link <?php echo ($active_page == 'settings') ? 'active' : ''; ?>">
            <i class="fa-solid fa-slider"></i> <span>Control Center</span>
        </a>
    </nav>
</div>

<div class="main-content">
    <div class="top-nav">
        <div class="mobile-admin-toggle" style="display: none;" onclick="document.querySelector('.sidebar').classList.toggle('active')">
            <i class="fa-solid fa-bars-staggered"></i>
        </div>
        <div class="page-info">
            <h1><?php echo $page_title; ?></h1>
        </div>
        
        <div class="user-profile">
            <div class="theme-toggle-admin" id="theme-toggle">
                <i class="fa-solid fa-moon"></i>
            </div>
            <div class="admin-badge">
                <i class="fa-solid fa-shield-check"></i>
                Root Administrator
            </div>
            <a href="logout.php" class="btn-logout">
                <i class="fa-solid fa-power-off"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
