<?php
session_start();
if(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: portal_dashboard.php");
    exit;
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    
    if($username) {
        // In a real application, you'd check if the user exists and send an email with a reset token.
        // Since we don't have an email server configured, we will simulate the success message.
        $success = "If an account with that ID exists, a password reset link has been sent to your registered email.";
    } else {
        $error = "Please enter your Username / Roll No.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Recovery - Everest School</title>
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

        .glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: pulse 15s infinite alternate;
        }

        @keyframes pulse {
            from { transform: scale(1); opacity: 0.1; }
            to { transform: scale(1.2); opacity: 0.2; }
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

        .icon-header {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .icon-box {
            width: 70px;
            height: 70px;
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            border: 1px solid rgba(245, 158, 11, 0.2);
            transform: rotate(-10deg);
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.25rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        .subtitle {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .alert {
            padding: 1.25rem;
            border-radius: 14px;
            font-size: 0.95rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .alert-error { background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }
        .alert-success { background: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2); }

        .form-group {
            margin-bottom: 2rem;
        }

        label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 700;
            font-size: 0.8rem;
            color: #64748b;
            text-transform: uppercase;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
        }

        input {
            width: 100%;
            background: var(--card-bg);
            border: 1px solid var(--border);
            padding: 1.15rem 1.25rem 1.15rem 3.5rem;
            border-radius: 14px;
            color: var(--text-main);
            font-family: inherit;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            border-color: #f59e0b;
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        }

        .btn-recover {
            width: 100%;
            padding: 1.25rem;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .btn-recover:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(245, 158, 11, 0.4); }

        .footer-links {
            margin-top: 3rem;
            text-align: center;
            font-size: 0.95rem;
            color: #64748b;
        }

        .footer-links a { color: #f59e0b; font-weight: 700; text-decoration: none; }

        .back-nav { margin-top: 2rem; text-align: center; }
        .back-nav a { color: #475569; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; }
        .back-nav a:hover { color: var(--text-main); }
    </style>
</head>
<body>

<div class="glow" style="top: -100px; left: -100px;"></div>
<div class="glow" style="bottom: -100px; right: -100px; background: radial-gradient(circle, rgba(79, 70, 229, 0.1) 0%, transparent 70%);"></div>

<div class="auth-card">
    <div class="icon-header">
        <div class="icon-box">
            <i class="fa-solid fa-key"></i>
        </div>
        <h1>Access Link</h1>
        <p class="subtitle">Secure Credential Recovery</p>
    </div>

    <?php if($error): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-paper-plane"></i>
            <div>
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        </div>
        <a href="login.php" class="btn-recover" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); margin-top: 1rem;">
            Return to Identifier Confirmation
        </a>
    <?php else: ?>
        <form method="POST">
            <div class="form-group">
                <label>Personnel Identifier / Username</label>
                <div class="input-wrapper">
                    <input type="text" name="username" placeholder="e.g. EVT-0987" required>
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
            </div>

            <button type="submit" class="btn-recover">
                Dispatch Recovery Link
                <i class="fa-solid fa-bolt"></i>
            </button>
        </form>
    <?php endif; ?>

    <div class="footer-links">
        Acknowledge Credentials? <a href="login.php">Sign In</a>
    </div>

    <div class="back-nav">
        <a href="index.php">
            <i class="fa-solid fa-chevron-left"></i>
            Return to Campus Home
        </a>
    </div>
</div>

<script src="js/main.js"></script>
</body>
</html>
