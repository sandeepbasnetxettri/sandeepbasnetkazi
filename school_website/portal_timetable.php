<?php
session_start();
if(!isset($_SESSION['user_logged_in']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit;
}
require_once 'config/db.php';
$user_id = $_SESSION['user_id'];

// Fetch student details
$stmt = $pdo->prepare("SELECT s.*, c.class_name FROM students s JOIN classes c ON s.class_id = c.id WHERE s.user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch();

if (!$student) {
    echo "Student profile not found.";
    exit;
}

$student_name = $student['name'];
$student_class = $student['class_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Schedule - Everest Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
        }

        .portal-nav {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 1.25rem 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .portal-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: white;
        }

        .portal-brand h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .logo-box {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 8px 16px -4px rgba(79, 70, 229, 0.4);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .avatar-wrap {
            position: relative;
            padding: 2px;
            background: linear-gradient(45deg, #4f46e5, #10b981);
            border-radius: 50%;
        }

        .user-menu img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: block;
            border: 2px solid #0f172a;
        }

        .btn-logout {
            color: #94a3b8;
            font-size: 1.25rem;
            transition: 0.3s;
        }

        .btn-logout:hover { color: #f87171; }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }

        .page-header {
            margin-bottom: 4rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .header-title h1 {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800;
            letter-spacing: -0.04em;
            margin-top: 0.5rem;
            color: var(--text-main);
        }

        .header-title span {
            color: #818cf8;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.25em;
            font-size: 0.85rem;
            display: block;
        }

        .timetable-container {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        }

        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 1100px;
        }

        th {
            background: var(--card-bg);
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-size: 0.7rem;
            padding: 1.5rem 1rem;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        td {
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            border-right: 1px solid rgba(255, 255, 255, 0.03);
            transition: 0.3s;
        }

        tr:last-child td { border-bottom: none; }
        td:last-child { border-right: none; }

        td:first-child, th:first-child {
            background: var(--bg-color);
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            color: #818cf8;
            font-size: 1rem;
            text-align: left;
            padding-left: 2.5rem;
            width: 160px;
            border-right: 1px solid var(--border);
        }

        .period-card {
            cursor: default;
        }

        .period-card:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .period-sub {
            display: block;
            font-weight: 800;
            color: var(--text-main);
            font-size: 1rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.01em;
        }

        .period-teacher {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: #64748b;
            background: rgba(15, 23, 42, 0.3);
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .lunch-break {
            background: rgba(245, 158, 11, 0.02) !important;
            color: #f59e0b;
            font-weight: 900;
            letter-spacing: 0.6em;
            text-transform: uppercase;
            font-size: 0.8rem;
            border-right: 1px solid rgba(245, 158, 11, 0.1) !important;
        }

        .extracurricular {
            background: rgba(16, 185, 129, 0.03) !important;
            color: #10b981;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 0.85rem 1.75rem;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.85rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.95rem;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(0,0,0,0.3);
            border-color: rgba(255,255,255,0.2);
        }

        @media (max-width: 1024px) {
            .portal-nav { padding: 1rem 1.5rem; }
            .container { padding: 3rem 1.5rem; }
            .page-header { margin-bottom: 3rem; }
        }
    </style>
</head>
<body>

<nav class="portal-nav">
    <a href="portal_dashboard.php" class="portal-brand">
        <div class="logo-box">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <h2>Everest Portal</h2>
    </a>
    <div class="user-menu">
        <div class="avatar-wrap">
            <img src="https://ui-avatars.com/api/?name=Aarav+Sharma&background=0f172a&color=fff" alt="User">
        </div>
        <a href="logout_portal.php" class="btn-logout" title="Secure Exit">
            <i class="fa-solid fa-power-off"></i>
        </a>
    </div>
</nav>

<div class="container reveal">
    <div class="page-header">
        <div class="header-title">
            <span>Pedagogical Lifecycle</span>
            <h1>Academic Schedule</h1>
        </div>
        <a href="portal_dashboard.php" class="btn-back">
            <i class="fa-solid fa-house-chimney-window"></i>
            Operational Hub
        </a>
    </div>

    <div class="timetable-container">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Operational Cycke</th>
                        <th>10:00 - 11:00</th>
                        <th>11:00 - 11:45</th>
                        <th>11:45 - 12:30</th>
                        <th>12:30 - 1:15</th>
                        <th>1:15 - 2:00</th>
                        <th>2:00 - 2:40</th>
                        <th>2:40 - 3:20</th>
                        <th>3:20 - 4:00</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sunday</td>
                        <td class="period-card"><span class="period-sub">Mathematics</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Sharma</span></td>
                        <td class="period-card"><span class="period-sub">English</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Ms. Rai</span></td>
                        <td class="period-card"><span class="period-sub">Science</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Dr. Patel</span></td>
                        <td class="period-card"><span class="period-sub">Nepali</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mrs. Adhikari</span></td>
                        <td rowspan="6" class="lunch-break" style="writing-mode: vertical-rl; transform: rotate(180deg);">Refectory Interval</td>
                        <td class="period-card"><span class="period-sub">Social Studies</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Thapa</span></td>
                        <td class="period-card"><span class="period-sub">Nepali</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mrs. Adhikari</span></td>
                        <td class="period-card"><span class="period-sub">Opt. Math</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Kumar</span></td>
                    </tr>
                    <tr>
                        <td>Monday</td>
                        <td class="period-card"><span class="period-sub">Mathematics</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Sharma</span></td>
                        <td class="period-card"><span class="period-sub">English</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Ms. Rai</span></td>
                        <td class="period-card"><span class="period-sub">Science</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Dr. Patel</span></td>
                        <td class="period-card"><span class="period-sub">Nepali</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mrs. Adhikari</span></td>
                        <td class="period-card"><span class="period-sub">Social Studies</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Thapa</span></td>
                        <td class="period-card"><span class="period-sub">Nepali</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mrs. Adhikari</span></td>
                        <td class="period-card"><span class="period-sub">Opt. Math</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Kumar</span></td>
                    </tr>
                    <tr>
                        <td>Tuesday</td>
                        <td class="period-card"><span class="period-sub">Mathematics</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Sharma</span></td>
                        <td class="period-card"><span class="period-sub">English</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Ms. Rai</span></td>
                        <td class="period-card"><span class="period-sub">Science</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Dr. Patel</span></td>
                        <td class="period-card"><span class="period-sub">Nepali</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mrs. Adhikari</span></td>
                        <td class="period-card"><span class="period-sub">Social Studies</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Thapa</span></td>
                        <td class="period-card"><span class="period-sub">Nepali</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mrs. Adhikari</span></td>
                        <td class="period-card"><span class="period-sub">Opt. Math</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Kumar</span></td>
                    </tr>
                    <tr>
                        <td>Wednesday</td>
                        <td class="period-card"><span class="period-sub">Mathematics</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Sharma</span></td>
                        <td class="period-card"><span class="period-sub">English</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Ms. Rai</span></td>
                        <td class="period-card"><span class="period-sub">Science</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Dr. Patel</span></td>
                        <td class="period-card"><span class="period-sub">Nepali</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mrs. Adhikari</span></td>
                        <td class="period-card"><span class="period-sub">Social Studies</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Thapa</span></td>
                        <td class="period-card"><span class="period-sub">Nepali</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mrs. Adhikari</span></td>
                        <td class="period-card"><span class="period-sub">Opt. Math</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Kumar</span></td>
                    </tr>
                    <tr>
                        <td>Thursday</td>
                        <td class="period-card"><span class="period-sub">Mathematics</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Sharma</span></td>
                        <td class="period-card"><span class="period-sub">English</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Ms. Rai</span></td>
                        <td class="period-card"><span class="period-sub">Science</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Dr. Patel</span></td>
                        <td class="period-card"><span class="period-sub">Nepali</span><span class="period-teacher"><i class="fa-solid fa-user-graduate"></i> Mrs. Adhikari</span></td>
                        <td class="period-card"><span class="period-sub">Social Studies</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Thapa</span></td>
                        <td class="period-card"><span class="period-sub">Nepali</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mrs. Adhikari</span></td>
                        <td class="period-card"><span class="period-sub">Opt. Math</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Kumar</span></td>
                    </tr>
                    <tr>
                        <td>Friday</td>
                        <td class="period-card"><span class="period-sub">Mathematics</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mr. Sharma</span></td>
                        <td class="period-card"><span class="period-sub">English</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Ms. Rai</span></td>
                        <td class="period-card"><span class="period-sub">Science</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Dr. Patel</span></td>
                        <td class="period-card"><span class="period-sub">Nepali</span><span class="period-teacher"><i class="fa-solid fa-id-badge"></i> Mrs. Adhikari</span></td>
                        <td colspan="3" class="extracurricular">
                            <span class="period-sub">Institutional Syndicates</span>
                            <span class="period-teacher"><i class="fa-solid fa-users-gear"></i> Specialized Teachers</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="js/main.js"></script>
</body>
</html>
