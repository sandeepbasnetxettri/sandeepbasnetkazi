<?php
session_start();
if(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: portal_dashboard.php");
    exit;
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/db.php';
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'student';
    $name = trim($_POST['name'] ?? '');
    
    if($username && $password && $confirm_password && $name) {
        if($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } elseif(strlen($password) < 6) {
             $error = "Password must be at least 6 characters long.";
        } else {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if($stmt->rowCount() > 0) {
                $error = "Username/Roll No. already exists. Please login instead.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                try {
                    $pdo->beginTransaction();
                    
                    // Insert into users
                    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                    $stmt->execute([$username, $hashed_password, $role]);
                    $user_id = $pdo->lastInsertId();
                    
                    // Depending on role, insert into students or teachers (Basic skeleton for now)
                    if($role === 'student') {
                        // Ensure at least one class exists to avoid foreign key errors
                        $stmt_class = $pdo->query("SELECT id FROM classes LIMIT 1");
                        $class = $stmt_class->fetch();
                        $class_id = $class ? $class['id'] : null;
                        
                        if(!$class_id) {
                            $pdo->exec("INSERT INTO classes (class_name) VALUES ('Class 10')");
                            $class_id = $pdo->lastInsertId();
                        }
                        
                        $stmt2 = $pdo->prepare("INSERT INTO students (user_id, roll_no, name, class_id) VALUES (?, ?, ?, ?)");
                        $stmt2->execute([$user_id, $username, $name, $class_id]);
                    } else if ($role === 'teacher') {
                        $stmt2 = $pdo->prepare("INSERT INTO teachers (user_id, name) VALUES (?, ?)");
                        $stmt2->execute([$user_id, $name]);
                    }
                    
                    $pdo->commit();
                    $success = "Registration successful! You can now login.";
                } catch(Exception $e) {
                    $pdo->rollBack();
                    $error = "Registration failed: " . $e->getMessage();
                }
            }
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
    <title>Academic Enrollment - Everest School</title>
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
            overflow: auto;
            transition: var(--transition);
        }

        .glow {
            position: absolute;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(100px);
            z-index: 0;
            animation: drift 25s infinite alternate;
        }

        @keyframes drift {
            from { transform: translate(-10%, -10%); }
            to { transform: translate(10%, 10%); }
        }

        .auth-card {
            width: 100%;
            max-width: 550px;
            background: var(--glass);
            backdrop-filter: var(--blur);
            -webkit-backdrop-filter: var(--blur);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            padding: 4rem 3rem;
            position: relative;
            z-index: 10;
            box-shadow: var(--shadow);
            margin: 2rem 0;
            transition: var(--transition);
        }

        .header-section {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .icon-box {
            width: 65px;
            height: 65px;
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin: 0 auto 1.5rem;
            border: 1px solid rgba(16, 185, 129, 0.2);
            transform: rotate(5deg);
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        .subtitle {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .full-width { grid-column: span 2; }

        .form-group {
            margin-bottom: 1.75rem;
        }

        label {
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
            color: var(--text-muted);
            transition: 0.3s;
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
            transition: 0.3s;
            outline: none;
        }

        select option { background: var(--bg-color); color: var(--text-main); }

        input:focus, select:focus {
            background: rgba(255, 255, 255, 0.05);
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        input:focus + i, select:focus + i {
            color: #10b981;
        }

        .btn-register {
            width: 100%;
            padding: 1.25rem;
            background: linear-gradient(135deg, #10b981, #059669);
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
            margin-top: 1rem;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.4);
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

        .footer-links {
            margin-top: 3rem;
            text-align: center;
            font-size: 0.95rem;
            color: #64748b;
        }

        .footer-links a {
            color: #10b981;
            font-weight: 700;
            text-decoration: none;
        }

        .back-nav {
            margin-top: 2rem;
            text-align: center;
        }

        .back-nav a {
            color: #475569;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: 0.3s;
        }

        .back-nav a:hover { color: var(--text-main); }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: span 1; }
            .auth-card { padding: 3rem 1.5rem; border-radius: 0; margin: 0; border: none; }
        }
    </style>
</head>
<body>

<div class="glow" style="top: -150px; left: -150px; background: radial-gradient(circle, rgba(79, 70, 229, 0.1) 0%, transparent 70%);"></div>
<div class="glow" style="bottom: -150px; right: -150px;"></div>

<div class="auth-card">
    <div class="header-section">
        <div class="icon-box">
            <i class="fa-solid fa-user-plus"></i>
        </div>
        <h1>Join Everest</h1>
        <p class="subtitle">Initialize Your Academic Identity</p>
    </div>

    <?php if($error): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <div>
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        </div>
        <a href="login.php" class="btn-register">Access Portal Now <i class="fa-solid fa-arrow-right"></i></a>
    <?php else: ?>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Account Specification</label>
                    <div class="input-wrapper">
                        <select name="role" required>
                            <option value="student">Student Profile</option>
                            <option value="teacher">Teacher Profile</option>
                        </select>
                        <i class="fa-solid fa-id-badge"></i>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Legal Identification Name</label>
                    <div class="input-wrapper">
                        <input type="text" name="name" placeholder="Full Name" required>
                        <i class="fa-solid fa-user-tag"></i>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Personnel Identifier (ID)</label>
                    <div class="input-wrapper">
                        <input type="text" name="username" placeholder="e.g. EVT-1024" required>
                        <i class="fa-solid fa-fingerprint"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Secure Passkey</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" placeholder="••••••••" required>
                        <i class="fa-solid fa-lock"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Verify Passkey</label>
                    <div class="input-wrapper">
                        <input type="password" name="confirm_password" placeholder="••••••••" required>
                        <i class="fa-solid fa-shield-check"></i>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-register">
                Finalize Enrollment
                <i class="fa-solid fa-user-check"></i>
            </button>
        </form>
    <?php endif; ?>

    <div class="footer-links">
        Already a member? <a href="login.php">Sign In</a>
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
