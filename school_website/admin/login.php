<?php
session_start();
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../config/db.php';
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if($username && $password) {
        $stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if($user && (password_verify($password, $user['password']) || $user['password'] === $password)) {
            if($user['role'] === 'admin') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                header("Location: index.php");
                exit;
            } else {
                $error = "Access denied. Admin privileges required.";
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
    <title>Identity Verification - Everest Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #818cf8;
            --secondary: #10b981;
            --bg: #0f172a;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        /* Abstract Particles */
        .particle {
            position: absolute;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            z-index: 0;
            animation: pulse 10s infinite alternate;
        }

        @keyframes pulse {
            0% { transform: scale(1) translate(0, 0); }
            100% { transform: scale(1.5) translate(10%, 10%); }
        }

        .login-card {
            width: 100%;
            max-width: 460px;
            background: var(--glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            padding: 4rem 3rem;
            position: relative;
            z-index: 10;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .logo-box {
            width: 70px;
            height: 70px;
            background: rgba(129, 140, 248, 0.1);
            color: var(--primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            border: 1px solid rgba(129, 140, 248, 0.2);
            transform: rotate(-10deg);
            transition: var(--transition);
        }

        .login-card:hover .logo-box {
            transform: rotate(0deg) scale(1.1);
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.25rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            margin-bottom: 0.5rem;
        }

        p.subtitle {
            color: #94a3b8;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 2.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 700;
            font-size: 0.8rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
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
            transition: var(--transition);
        }

        input {
            width: 100%;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            padding: 1.15rem 1.25rem 1.15rem 3.5rem;
            border-radius: 14px;
            color: white;
            font-family: inherit;
            font-size: 1rem;
            transition: var(--transition);
            outline: none;
        }

        input:focus {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.1);
        }

        input:focus + i {
            color: var(--primary);
        }

        .btn-auth {
            width: 100%;
            padding: 1.25rem;
            background: linear-gradient(135deg, var(--primary), #6366f1);
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 1rem;
            box-shadow: 0 10px 15px -3px rgba(129, 140, 248, 0.3);
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(129, 140, 248, 0.4);
        }

        .error-msg {
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
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .footer-links {
            margin-top: 3rem;
            text-align: center;
        }

        .footer-links a {
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .footer-links a:hover {
            color: white;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card { padding: 3rem 1.5rem; border-radius: 0; min-height: 100vh; display: flex; flex-direction: column; justify-content: center; border: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

<div class="particle" style="width: 500px; height: 500px; top: -100px; left: -100px;"></div>
<div class="particle" style="width: 400px; height: 400px; bottom: -100px; right: -100px; animation-delay: -5s;"></div>

<div class="login-card">
    <div class="logo-section">
        <div class="logo-box">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <h1>Identity Verification</h1>
        <p class="subtitle">Institutional Command Center</p>
    </div>

    <?php if($error): ?>
        <div class="error-msg">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Personnel Identifier</label>
            <div class="input-wrapper">
                <input type="text" name="username" placeholder="Staff ID / Username" required autocomplete="username">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>
        
        <div class="form-group">
            <label>Security Passkey</label>
            <div class="input-wrapper">
                <input type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                <i class="fa-solid fa-key"></i>
            </div>
        </div>

        <button type="submit" class="btn-auth">
            De-encrypt & Authorize
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
        </button>
    </form>

    <div class="footer-links">
        <a href="../index.php">
            <i class="fa-solid fa-arrow-left"></i>
            Restore Portal Access
        </a>
    </div>
</div>

<script>
    document.querySelector('form').onsubmit = function() {
        const btn = this.querySelector('button');
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Authorizing...';
        btn.style.opacity = '0.8';
        btn.style.pointerEvents = 'none';
    };
</script>

</body>
</html>
