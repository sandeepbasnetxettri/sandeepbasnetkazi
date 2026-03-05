<?php
session_start();
if(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/db.php';
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'student';
    
    if($username && $password) {
        // In a real app, you would verify against the hashed password
        // For demonstration, we simply check the username
        $stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE username = ? AND role = ?");
        $stmt->execute([$username, $role]);
        $user = $stmt->fetch();
        
        // This is simplified. Ideally use password_verify($password, $user['password'])
        if($user && (password_verify($password, $user['password']) || $user['password'] === $password)) {
            if ($user['role'] === 'admin') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                header("Location: admin/index.php");
                exit;
            } else {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                header("Location: portal_dashboard.php");
                exit;
            }
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Institutional Access - Everest School</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script>
        // Immediate theme application to prevent flash
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <style>
        body {
            background: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: var(--transition);
        }

        /* Ambient Glow */
        .glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: move 20s infinite alternate;
        }

        @keyframes move {
            from { transform: translate(-20%, -20%); }
            to { transform: translate(20%, 20%); }
        }

        .auth-card {
            width: 100%;
            max-width: 480px;
            background: var(--glass);
            backdrop-filter: var(--blur);
            -webkit-backdrop-filter: var(--blur);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            padding: 4rem 3rem;
            position: relative;
            z-index: 10;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .brand-section {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .brand-icon {
            width: 65px;
            height: 65px;
            background: rgba(79, 70, 229, 0.1);
            color: #818cf8;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin: 0 auto 1.5rem;
            border: 1px solid rgba(79, 70, 229, 0.2);
            transform: rotate(-10deg);
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        p.subtitle {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 2rem;
            position: relative;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            transition: all 0.3s ease;
        }

        input, select {
            width: 100%;
            background: var(--card-bg);
            border: 1px solid var(--border);
            padding: 1.15rem 1.25rem 1.15rem 3.5rem;
            border-radius: 14px;
            color: var(--text-main);
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
            cursor: pointer;
        }

        select option {
            background: var(--bg-color);
            color: var(--text-main);
        }

        input:focus, select:focus {
            background: rgba(255, 255, 255, 0.05);
            border-color: #818cf8;
            box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.1);
        }

        input:focus + i, select:focus + i {
            color: #818cf8;
        }

        .btn-auth {
            width: 100%;
            padding: 1.25rem;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4);
        }

        .error-alert {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            padding: 1rem;
            border-radius: 14px;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
            margin-bottom: 2rem;
        }

        .links-area {
            margin-top: 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: #64748b;
        }

        .links-area a {
            color: #818cf8;
            font-weight: 700;
            text-decoration: none;
        }

        .back-home {
            margin-top: 2rem;
            text-align: center;
        }

        .back-home a {
            color: #475569;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: 0.3s;
        }

        .back-home a:hover {
            color: var(--text-main);
        }
    </style>
</head>
<body>

<div class="glow" style="top: -100px; left: -100px;"></div>
<div class="glow" style="bottom: -100px; right: -100px; animation-delay: -5s; background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);"></div>

<div class="auth-card">
    <div class="brand-section">
        <div class="brand-icon">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <h1>Portal Access</h1>
        <p class="subtitle">Unified Academic Gateway</p>
    </div>

    <?php if($error): ?>
        <div class="error-alert">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <div class="input-wrapper">
                <select name="role" required>
                    <option value="" disabled selected>Identify Perspective</option>
                    <option value="student">Student Account</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Administrator</option>
                </select>
                <i class="fa-solid fa-users-viewfinder"></i>
            </div>
        </div>

        <div class="form-group">
            <div class="input-wrapper">
                <input type="text" name="username" placeholder="Personnel ID / Username" required autocomplete="username">
                <i class="fa-solid fa-id-card-clip"></i>
            </div>
        </div>
        
        <div class="form-group">
            <div class="input-wrapper">
                <input type="password" name="password" placeholder="Passkey" required autocomplete="current-password">
                <i class="fa-solid fa-fingerprint"></i>
            </div>
        </div>

        <button type="submit" class="btn-auth">
            Unlock Dashboard
            <i class="fa-solid fa-circle-chevron-right"></i>
        </button>
    </form>

    <div class="links-area">
        <span>New member? <a href="register.php">Apply Now</a></span>
        <a href="forgot_password.php">Recover Access</a>
    </div>

    <div class="back-home">
        <a href="index.php">
            <i class="fa-solid fa-arrow-left"></i>
            Restore Campus Overview
        </a>
    </div>
</div>

<script src="js/main.js"></script>
</body>
</html>
