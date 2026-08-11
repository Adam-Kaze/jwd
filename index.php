<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>DKV ROOM — Your Creative Space.</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700;800;900&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />

    <style>
        /* ==========================================
           RESET & BASE
           ========================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            color: #f0f0f0;
            overflow: hidden;
        }

        /* ==========================================
           HERO — DENGAN BACKGROUND IMAGE
           ========================================== */
        .hero {
            position: relative;
            width: 100%;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
            display: flex;
            align-items: center;
            
            /* ===== BACKGROUND IMAGE UTAMA ===== */
            background-image: url('bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            
            /* fallback jika gambar tidak ditemukan */
            background-color: #0b0b0b;
        }

        /* ===== OVERLAY GELAP UNTUK READABILITY ===== */
        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1;
            background: 
                /* Overlay gelap agar teks terbaca */
                linear-gradient(135deg, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.55) 50%, rgba(0,0,0,0.70) 100%),
                /* tetap ada sentuhan DKV: grid halus & aksen emas */
                repeating-linear-gradient(45deg, 
                    rgba(212, 175, 55, 0.03) 0px, 
                    rgba(212, 175, 55, 0.03) 2px, 
                    transparent 2px, 
                    transparent 8px),
                repeating-linear-gradient(-45deg, 
                    rgba(212, 175, 55, 0.02) 0px, 
                    rgba(212, 175, 55, 0.02) 1px, 
                    transparent 1px, 
                    transparent 12px);
            pointer-events: none;
        }

        /* ===== AKSEN GLOW EMAS ===== */
        .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1;
            background: 
                radial-gradient(circle at 20% 30%, rgba(212, 175, 55, 0.10), transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(212, 175, 55, 0.06), transparent 40%),
                radial-gradient(circle at 50% 90%, rgba(212, 175, 55, 0.04), transparent 30%);
            pointer-events: none;
        }

        /* ===== FLOATING GLOW ORB ===== */
        .glow-orb {
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.08), transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            filter: blur(100px);
            z-index: 1;
            animation: pulseGlow 12s ease-in-out infinite;
            pointer-events: none;
        }

        .container {
            width: 1200px;
            max-width: 95%;
            margin: 0 auto;
            position: relative;
            z-index: 10;
        }

        /* ==========================================
           NAVBAR
           ========================================== */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 0 12px 0;
            border-bottom: 1px solid rgba(212, 175, 55, 0.15);
            flex-wrap: wrap;
            gap: 8px;
            backdrop-filter: blur(4px);
            background: rgba(0,0,0,0.2);
            border-radius: 16px;
            padding: 12px 20px;
            position: relative;
            z-index: 12;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d4af37, #f5d77b);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0b0b0b;
            font-size: 16px;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.05;
        }

        .logo-text span:nth-child(1) {
            font-size: 17px;
            font-weight: 800;
            color: #f5f5f5;
            letter-spacing: 0.3px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        .logo-text span:nth-child(2) {
            font-size: 9px;
            letter-spacing: 2.5px;
            font-weight: 700;
            color: #d4af37;
            text-transform: uppercase;
            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
        }

        .menu {
            display: flex;
            gap: 28px;
            list-style: none;
        }

        .menu a {
            font-size: 13px;
            font-weight: 500;
            color: #c0c0c0;
            transition: .3s;
            position: relative;
            text-shadow: 0 2px 8px rgba(0,0,0,0.6);
        }

        .menu a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: #d4af37;
            transition: .3s;
        }

        .menu a:hover::after {
            width: 100%;
        }

        .menu a:hover {
            color: #d4af37;
        }

        .nav-action {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }

        .btn-login {
            height: 38px;
            padding: 0 22px;
            border-radius: 10px;
            background: #d4af37;
            color: #0b0b0b;
            font-weight: 700;
            font-size: 13px;
            transition: .35s;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.5);
            background: #e8c44a;
        }

        /* ==========================================
           HERO WRAPPER — CENTERED
           ========================================== */
        .hero-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
            height: calc(100vh - 80px);
            height: calc(100dvh - 80px);
            position: relative;
            z-index: 10;
        }

        .hero-center {
            max-width: 620px;
            width: 100%;
            text-align: center;
            padding: 30px 35px;
            background: rgba(0,0,0,0.35);
            backdrop-filter: blur(12px);
            border-radius: 32px;
            border: 1px solid rgba(212, 175, 55, 0.15);
            box-shadow: 0 30px 80px rgba(0,0,0,0.6);
        }

        /* ==========================================
           CONTENT
           ========================================== */
        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 56px;
            font-weight: 900;
            line-height: 1.08;
            color: #f5f5f5;
            margin-bottom: 16px;
            animation: fadeInUp 0.8s ease 0.1s both;
            text-shadow: 0 4px 30px rgba(0,0,0,0.5);
        }

        .hero-title .highlight {
            background: linear-gradient(135deg, #d4af37, #f5d77b, #d4af37);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 3s ease-in-out infinite;
            text-shadow: 0 4px 30px rgba(212, 175, 55, 0.2);
        }

        .hero-subtitle {
            font-size: 18px;
            line-height: 1.7;
            color: #e0e0e0;
            margin-bottom: 32px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 0.8s ease 0.2s both;
            text-shadow: 0 2px 15px rgba(0,0,0,0.5);
        }

        .hero-subtitle strong {
            color: #f5f5f5;
            font-weight: 600;
        }

        .button-wrapper {
            animation: fadeInUp 0.8s ease 0.3s both;
        }

        .btn-catalog {
            padding: 16px 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #d4af37, #e8c44a);
            color: #0b0b0b;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.4s ease;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 35px rgba(212, 175, 55, 0.3);
        }

        .btn-catalog:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 50px rgba(212, 175, 55, 0.5);
            background: linear-gradient(135deg, #e0c040, #f5d77b);
        }

        .btn-catalog:active {
            transform: translateY(0px);
        }

        /* ==========================================
           NOTIFICATION
           ========================================== */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 12px;
            background: rgba(26, 26, 26, 0.9);
            border: 1px solid rgba(212, 175, 55, 0.25);
            color: #f5f5f5;
            font-size: 14px;
            transform: translateX(120%);
            transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 999;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(15px);
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification .notif-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #d4af37;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0b0b0b;
            font-size: 14px;
            flex-shrink: 0;
        }

        /* ==========================================
           ANIMATIONS
           ========================================== */
        @keyframes pulseGlow {
            0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.5; }
            50% { transform: translate(-50%, -50%) scale(1.15); opacity: 0.9; }
        }

        @keyframes shimmer {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ==========================================
           RESPONSIVE
           ========================================== */
        @media (max-width: 1100px) {
            .hero-title {
                font-size: 44px;
            }
        }

        @media (max-width: 768px) {
            html, body {
                overflow-y: auto;
            }

            .hero {
                height: auto;
                min-height: 100vh;
                min-height: 100dvh;
                align-items: flex-start;
                padding: 10px 0 30px;
                background-attachment: fixed;
            }

            .hero-wrapper {
                height: auto;
                min-height: calc(100vh - 70px);
                min-height: calc(100dvh - 70px);
                padding: 10px 0 20px;
                align-items: center;
            }

            .hero-center {
                padding: 24px 16px;
                backdrop-filter: blur(10px);
                background: rgba(0,0,0,0.45);
                border-radius: 24px;
                margin-top: 10px;
            }

            .navbar {
                padding: 12px 16px;
                gap: 6px;
                backdrop-filter: blur(6px);
                background: rgba(0,0,0,0.3);
            }

            .menu {
                display: none;
            }

            .logo-text span:nth-child(1) {
                font-size: 15px;
            }

            .logo-icon {
                width: 30px;
                height: 30px;
                font-size: 13px;
            }

            .btn-login {
                height: 34px;
                padding: 0 16px;
                font-size: 12px;
            }

            .hero-title {
                font-size: 32px;
            }

            .hero-subtitle {
                font-size: 15px;
                padding: 0 10px;
            }

            .btn-catalog {
                padding: 14px 32px;
                font-size: 14px;
                width: 100%;
                justify-content: center;
            }

            .notification {
                top: 10px;
                right: 10px;
                left: 10px;
                padding: 14px 18px;
                font-size: 13px;
            }

            .glow-orb {
                width: 400px;
                height: 400px;
                filter: blur(100px);
            }
        }

        @media (max-width: 400px) {
            .hero-title {
                font-size: 26px;
            }

            .hero-subtitle {
                font-size: 13px;
            }
        }
    </style>
</head>

<body>

    <!-- ==========================================
    NOTIFICATION
    ========================================== -->
    <div id="notification" class="notification">
        <div class="notif-icon">
            <i class="fa-solid fa-check"></i>
        </div>
        <span id="notifMessage">Selamat datang di DKV ROOM!</span>
    </div>

    <!-- ==========================================
    HERO
    ========================================== -->
    <section class="hero">
        <!-- Glow orb untuk nuansa DKV -->
        <div class="glow-orb"></div>

        <div class="container">

            <!-- ===== NAVBAR ===== -->
            <nav class="navbar">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fa-solid fa-brush"></i>
                    </div>
                    <div class="logo-text">
                        <span>DKV ROOM</span>
                        <span>Desain · Ilustrasi · Foto · Video</span>
                    </div>
                </div>

                <div class="nav-action">
                    <button class="btn-login" onclick="location.href='katalog/admin/login.php'">
                        <i class="fa-solid fa-key"></i> Masuk
                    </button>
                </div>
            </nav>

            <!-- ===== HERO CONTENT ===== -->
            <div class="hero-wrapper">
                <div class="hero-center">

                    <!-- Title -->
                    <h1 class="hero-title">
                        Welcome to <br>
                        <span class="highlight">DKV ROOM</span>
                    </h1>
                    <p class="hero-subtitle">
                        Temukan karya desain, ilustrasi, foto, dan video terbaik.<br />
                        <strong>Jelajahi portofolio kami</strong> dan temukan inspirasi untuk proyek Anda berikutnya.
                    </p>

                    <!-- Button Layanan -->
                    <div class="button-wrapper">
                        <button class="btn-catalog" onclick="window.location.href='katalog/'">
                            <i class="fa-solid fa-briefcase"></i>
                            Lihat Layanan
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- ==========================================
    JAVASCRIPT
    ========================================== -->
    <script>
        // ===== NOTIFICATION =====
        function showNotification(message) {
            const notif = document.getElementById('notification');
            const msg = document.getElementById('notifMessage');
            
            msg.textContent = message;
            notif.classList.add('show');
            
            clearTimeout(window.notifTimeout);
            window.notifTimeout = setTimeout(() => {
                notif.classList.remove('show');
            }, 4000);
        }

        // ===== CLOSE NOTIFICATION ON CLICK =====
        document.getElementById('notification').addEventListener('click', function() {
            this.classList.remove('show');
        });

        // ===== SMOOTH SCROLL MENU =====
        document.querySelectorAll('.menu a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                } else {
                    showNotification('✨ Halaman ini adalah hero section. Semua konten ada di sini!');
                }
            });
        });

        // ===== RIPPLE EFFECT =====
        document.querySelectorAll('.btn-login, .btn-catalog').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);

                ripple.style.cssText = `
                    width: ${size}px;
                    height: ${size}px;
                    left: ${e.clientX - rect.left - size/2}px;
                    top: ${e.clientY - rect.top - size/2}px;
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255,255,255,.2);
                    transform: scale(0);
                    animation: ripple .6s linear;
                    pointer-events: none;
                `;

                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);

                setTimeout(() => ripple.remove(), 600);
            });
        });

        // ===== ADD RIPPLE KEYFRAMES =====
        const style = document.createElement('style');
        style.innerHTML = `
            @keyframes ripple {
                to { transform: scale(4); opacity: 0; }
            }
        `;
        document.head.appendChild(style);


    </script>

</body>
</html>