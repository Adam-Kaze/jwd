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
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            
            /* ===== BACKGROUND IMAGE UTAMA ===== */
            background-image: url('../../bg2.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            
            /* Fallback jika gambar tidak ditemukan */
            background-color: #0b0b0b;
            
            /* Untuk efek parallax ringan */
            background-attachment: fixed;
            
            position: relative;
        }

        /* ===== OVERLAY GELAP UNTUK READABILITY ===== */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: 0;
            background: 
                /* Overlay gelap agar teks terbaca */
                linear-gradient(135deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.70) 50%, rgba(0,0,0,0.80) 100%),
                /* Sentuhan DKV: grid halus */
                repeating-linear-gradient(45deg, 
                    rgba(212, 175, 55, 0.02) 0px, 
                    rgba(212, 175, 55, 0.02) 2px, 
                    transparent 2px, 
                    transparent 8px),
                repeating-linear-gradient(-45deg, 
                    rgba(212, 175, 55, 0.015) 0px, 
                    rgba(212, 175, 55, 0.015) 1px, 
                    transparent 1px, 
                    transparent 12px);
            pointer-events: none;
        }

        /* ===== AKSEN GLOW EMAS ===== */
        body::after {
            content: "";
            position: fixed;
            inset: 0;
            z-index: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(212, 175, 55, 0.08), transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(212, 175, 55, 0.05), transparent 40%),
                radial-gradient(circle at 50% 90%, rgba(212, 175, 55, 0.04), transparent 30%);
            pointer-events: none;
        }

        /* ===== FLOATING GLOW ORB ===== */
        .glow-orb {
            position: fixed;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.06), transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            filter: blur(100px);
            z-index: 0;
            animation: pulseGlow 10s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes pulseGlow {
            0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.4; }
            50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.8; }
        }

        /* ===== LOGIN BOX ===== */
        .login-box {
            position: relative;
            z-index: 1;
            background: rgba(11, 11, 11, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(212, 175, 55, 0.12);
            padding: 2.5rem;
            border-radius: 24px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.6);
            animation: fadeInUp 0.8s ease both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .login-box .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-box .logo i {
            font-size: 2.5rem;
            color: #d4af37;
            text-shadow: 0 0 30px rgba(212, 175, 55, 0.2);
        }
        .login-box .logo h2 {
            color: #f5f5f5;
            font-size: 1.5rem;
            margin-top: 0.5rem;
            font-weight: 700;
        }
        .login-box .logo p {
            color: rgba(212, 175, 55, 0.5);
            font-size: 0.75rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .form-group { 
            margin-bottom: 1.2rem; 
        }
        label {
            display: block;
            margin-bottom: 0.4rem;
            font-weight: 500;
            color: #d0d0d0;
            font-size: 0.85rem;
        }
        label i {
            color: #d4af37;
            margin-right: 8px;
        }
        input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            background: rgba(255,255,255,0.03);
            color: #f5f5f5;
            font-size: 1rem;
            transition: 0.3s;
            outline: none;
        }
        input:focus {
            border-color: rgba(212, 175, 55, 0.3);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.05);
            background: rgba(255,255,255,0.05);
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
            box-shadow: 0 8px 30px rgba(212, 175, 55, 0.3);
            background: linear-gradient(135deg, #e0c040, #c4a032);
        }
        button:active {
            transform: translateY(0);
        }
        .error {
            background: rgba(239, 68, 68, 0.08);
            color: #f87171;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            text-align: center;
            border-left: 3px solid #ef4444;
        }
        .btn-back {
            display: block;
            width: 100%;
            padding: 0.75rem;
            margin-top: 0.75rem;
            background: transparent;
            color: rgba(212, 175, 55, 0.7);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }
        .btn-back:hover {
            background: rgba(212, 175, 55, 0.08);
            border-color: rgba(212, 175, 55, 0.5);
            color: #d4af37;
            transform: translateY(-1px);
        }
        .footer-text {
            margin-top: 1.5rem;
            font-size: 0.7rem;
            text-align: center;
            color: rgba(255,255,255,0.15);
            letter-spacing: 1px;
        }
        .footer-text strong {
            color: rgba(212, 175, 55, 0.4);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            body {
                background-attachment: scroll;
                padding: 20px;
            }

            .login-box {
                padding: 2rem 1.5rem;
                max-width: 100%;
                backdrop-filter: blur(12px);
            }

            .login-box .logo i {
                font-size: 2rem;
            }
            .login-box .logo h2 {
                font-size: 1.3rem;
            }

            .glow-orb {
                width: 300px;
                height: 300px;
                filter: blur(80px);
            }
        }

        @media (max-width: 400px) {
            .login-box {
                padding: 1.5rem 1rem;
            }
            input {
                padding: 0.6rem 0.8rem;
                font-size: 0.9rem;
            }
            button {
                padding: 0.7rem;
                font-size: 0.9rem;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <!-- Glow orb untuk nuansa DKV -->
    <div class="glow-orb"></div>

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
                <label><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required autofocus>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit"><i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i> Masuk</button>
        </form>
        <a href="../../index.php" class="btn-back"><i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Kembali ke Halaman Utama</a>
        <div class="footer-text">
            &copy; <?= date('Y') ?> <strong>DKV ROOM</strong>
        </div>
    </div>
</body>
</html>