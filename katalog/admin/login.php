<?php
// Hapus session_start() di sini karena config.php sudah menangani session
require_once '../config.php';

/** @var mysqli $conn */
global $conn;

// Cek apakah sudah login
if (is_admin()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if ($user && password_verify($password, $user['password'])) {
        // Regenerasi session ID untuk keamanan
        session_regenerate_id(true);
        
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['login_time'] = time();
        
        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - DKV ROOM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0b0b0b;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-box {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(212, 175, 55, 0.08);
            padding: 2.5rem;
            border-radius: 24px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 35px rgba(0,0,0,0.4);
        }
        .login-box .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-box .logo i {
            font-size: 2.5rem;
            color: #d4af37;
        }
        .login-box .logo h2 {
            color: #f5f5f5;
            font-size: 1.5rem;
            margin-top: 0.5rem;
        }
        .login-box .logo p {
            color: rgba(255,255,255,0.3);
            font-size: 0.75rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .form-group { margin-bottom: 1.2rem; }
        label {
            display: block;
            margin-bottom: 0.4rem;
            font-weight: 500;
            color: #d0d0d0;
            font-size: 0.85rem;
        }
        input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            background: rgba(255,255,255,0.02);
            color: #f5f5f5;
            font-size: 1rem;
            transition: 0.3s;
            outline: none;
        }
        input:focus {
            border-color: rgba(212, 175, 55, 0.2);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.04);
        }
        input::placeholder {
            color: rgba(255,255,255,0.15);
        }
        button {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(135deg, #d4af37, #b8962e);
            color: #0b0b0b;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.2);
        }
        .error {
            background: rgba(239, 68, 68, 0.06);
            color: #f87171;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            text-align: center;
            border-left: 3px solid #ef4444;
        }
        .footer-text {
            margin-top: 1.5rem;
            font-size: 0.7rem;
            text-align: center;
            color: rgba(255,255,255,0.15);
            letter-spacing: 1px;
        }
        .footer-text strong {
            color: rgba(255,255,255,0.3);
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <i class="fas fa-certificate"></i>
            <h2>Admin Login</h2>
            <p>DKV ROOM</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-user" style="margin-right: 8px; color: #d4af37;"></i> Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required autofocus>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock" style="margin-right: 8px; color: #d4af37;"></i> Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit"><i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i> Masuk</button>
        </form>
        <div class="footer-text">
            &copy; <?= date('Y') ?> <strong>DKV ROOM</strong>
        </div>
    </div>
</body>
</html>