<?php
include '../config.php';

/** @var mysqli $conn */
global $conn;

// Ambil ID produk dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// --- KEAMANAN: Ambil data produk dengan Prepared Statement ---
$stmt = mysqli_prepare($conn, "SELECT p.*, c.name as category_name 
                               FROM products p 
                               JOIN categories c ON p.category_id = c.id 
                               WHERE p.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Jika produk tidak ditemukan
if (!$product) {
    header('Location: ../index.php');
    exit;
}

// --- KEAMANAN: Ambil nomor WhatsApp default dari database ---
$wa_default = '6281383796300';
$stmt_wa = mysqli_prepare($conn, "SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1");
$key_wa = 'wa_number';
mysqli_stmt_bind_param($stmt_wa, "s", $key_wa);
mysqli_stmt_execute($stmt_wa);
$result_wa = mysqli_stmt_get_result($stmt_wa);
if ($result_wa && mysqli_num_rows($result_wa) > 0) {
    $wa_default = mysqli_fetch_assoc($result_wa)['setting_value'];
}
mysqli_stmt_close($stmt_wa);

// Gunakan nomor WA khusus produk jika ada, jika tidak pakai default
$wa_number = !empty($product['wa_number']) ? $product['wa_number'] : $wa_default;

// ========== PERBAIKAN PATH GAMBAR ==========
$image_url = $product['image_url'];
$valid_image = false;

if (!empty($image_url)) {
    // Cek apakah ini path lokal (uploads/...)
    if (strpos($image_url, 'uploads/') === 0) {
        // Path lengkap ke file gambar dari root
        $full_path = dirname(__DIR__) . '/' . $image_url;
        
        // Cek apakah file benar-benar ada
        if (file_exists($full_path)) {
            $valid_image = true;
            // Gunakan path relatif dari folder detail ke uploads
            $image_src = '../' . $image_url;
        } else {
            // File tidak ditemukan, cek ekstensi case-sensitive
            $full_path_lower = strtolower($full_path);
            if (file_exists($full_path_lower)) {
                $valid_image = true;
                $image_src = '../' . $image_url;
            } else {
                $valid_image = false;
            }
        }
    } 
    // Cek apakah ini URL lengkap (http:// atau https://)
    elseif (strpos($image_url, 'http') === 0) {
        $valid_image = true;
        $image_src = $image_url;
    }
}

// Jika tidak ada gambar valid, gunakan placeholder dengan tema DKV ROOM
if (!$valid_image) {
    // Warna-warna tema DKV ROOM
    $colors = ['1a1a2e', '16213e', '0f0f1f', '1f1f3a', '2a2a4a'];
    $color = $colors[abs(crc32($product['name'])) % count($colors)];
    $image_src = 'https://placehold.co/800x400/' . $color . '/d4af37?text=🎨+' . rawurlencode(substr($product['name'], 0, 25));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($product['name']) ?> - DKV ROOM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        /* ==========================================
           RESET & BASE — DARK GOLD THEME
           ========================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #0b0b0b;
            min-height: 100vh;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(212, 175, 55, 0.08);
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }
        
        @keyframes fadeIn {
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
           PRODUCT IMAGE
           ========================================== */
        .product-image-container {
            position: relative;
            background: linear-gradient(135deg, #0f0f1f 0%, #1a1a2e 50%, #0f0f1f 100%);
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .product-image-container::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 40%, rgba(212, 175, 55, 0.04) 0%, transparent 60%);
            pointer-events: none;
        }
        
        .product-image {
            width: 100%;
            height: 420px;
            object-fit: cover;
            display: block;
            transition: transform 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            filter: brightness(0.85) saturate(1.05);
        }
        
        .product-image-container:hover .product-image {
            transform: scale(1.03);
            filter: brightness(1) saturate(1.1);
        }
        
        /* Badge di atas gambar */
        .product-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            background: linear-gradient(135deg, #d4af37, #f5d77b);
            color: #0b0b0b;
            padding: 6px 18px;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            z-index: 5;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .product-badge i {
            font-size: 0.6rem;
        }
        
        /* ==========================================
           PRODUCT INFO
           ========================================== */
        .product-info {
            padding: 2.5rem;
            background: rgba(255,255,255,0.01);
        }
        
        /* Breadcrumb */
        .breadcrumb {
            margin-bottom: 1.2rem;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .breadcrumb a {
            color: rgba(255,255,255,0.3);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .breadcrumb a:hover {
            color: #d4af37;
        }
        
        .breadcrumb i {
            font-size: 0.6rem;
            color: rgba(255,255,255,0.1);
        }
        
        .breadcrumb span {
            color: rgba(255,255,255,0.2);
        }
        
        /* Category Badge */
        .product-category {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(212, 175, 55, 0.06);
            color: #d4af37;
            padding: 0.3rem 1rem;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 1.2rem;
            letter-spacing: 0.3px;
            border: 1px solid rgba(212, 175, 55, 0.06);
        }
        
        .product-category i {
            font-size: 0.6rem;
            color: #d4af37;
        }
        
        /* Title */
        .product-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.4rem;
            margin-bottom: 0.75rem;
            color: #f5f5f5;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }
        
        .product-title strong {
            color: #d4af37;
        }
        
        /* Price */
        .product-price {
            font-size: 1.8rem;
            font-weight: 800;
            color: #d4af37;
            margin-bottom: 1.5rem;
            display: inline-block;
            background: rgba(212, 175, 55, 0.04);
            padding: 0.3rem 1.2rem;
            border-radius: 60px;
            border: 1px solid rgba(212, 175, 55, 0.06);
            font-family: 'Playfair Display', serif;
        }
        
        /* Description */
        .product-description {
            color: rgba(255,255,255,0.5);
            line-height: 1.9;
            margin-bottom: 2rem;
            font-size: 1rem;
            max-width: 700px;
        }
        
        /* ==========================================
           BUTTON GROUP
           ========================================== */
        .button-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }
        
        .btn-demo {
            background: rgba(255,255,255,0.04);
            color: #f5f5f5;
            padding: 0.8rem 1.8rem;
            border-radius: 60px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.06);
            cursor: pointer;
            font-size: 0.85rem;
        }
        
        .btn-demo i {
            font-size: 0.85rem;
            color: #d4af37;
        }
        
        .btn-demo:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(212, 175, 55, 0.15);
            transform: translateY(-2px);
        }
        
        .btn-cart {
            background: rgba(212, 175, 55, 0.06);
            color: #d4af37;
            padding: 0.8rem 1.8rem;
            border-radius: 60px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(212, 175, 55, 0.1);
            cursor: pointer;
            font-size: 0.85rem;
        }
        
        .btn-cart i {
            font-size: 0.85rem;
        }
        
        .btn-cart:hover:not(:disabled) {
            background: rgba(212, 175, 55, 0.12);
            border-color: rgba(212, 175, 55, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.1);
        }
        
        .btn-cart:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }
        
        .btn-wa {
            background: linear-gradient(135deg, #25D366, #1da85e);
            color: #0b0b0b;
            padding: 0.8rem 1.8rem;
            border-radius: 60px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.15);
            font-size: 0.85rem;
        }
        
        .btn-wa i {
            font-size: 0.85rem;
        }
        
        .btn-wa:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.25);
            background: linear-gradient(135deg, #1da85e, #158a4d);
        }
        
        /* ==========================================
           DIVIDER
           ========================================== */
        .divider {
            margin: 1.5rem 0;
            border: none;
            height: 1px;
            background: linear-gradient(90deg, rgba(255,255,255,0.02), rgba(212, 175, 55, 0.08), rgba(255,255,255,0.02));
        }
        
        /* ==========================================
           BACK BUTTON
           ========================================== */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            color: rgba(255,255,255,0.2);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }
        
        .btn-back i {
            transition: transform 0.3s ease;
        }
        
        .btn-back:hover {
            color: #d4af37;
        }
        
        .btn-back:hover i {
            transform: translateX(-4px);
        }
        
        /* ==========================================
           FEATURE LIST
           ========================================== */
        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
            padding: 1rem 0;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.3);
            font-size: 0.8rem;
        }
        
        .feature-item i {
            color: #d4af37;
            font-size: 0.9rem;
            width: 24px;
        }
        
        /* ==========================================
           TOAST NOTIFICATION
           ========================================== */
        .toast-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            z-index: 1000;
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
            animation: toastIn 0.4s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(255,255,255,0.06);
        }
        
        @keyframes toastIn {
            from { transform: translateX(100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        /* ==========================================
           RESPONSIVE
           ========================================== */
        @media (max-width: 992px) {
            body {
                padding: 16px;
            }
            
            .product-image {
                height: 350px;
            }
            
            .product-info {
                padding: 2rem;
            }
            
            .product-title {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 12px;
            }
            
            .container {
                border-radius: 24px;
            }
            
            .product-image {
                height: 260px;
            }
            
            .product-info {
                padding: 1.5rem;
            }
            
            .product-title {
                font-size: 1.6rem;
            }
            
            .product-price {
                font-size: 1.3rem;
                padding: 0.25rem 1rem;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn-demo, .btn-cart, .btn-wa {
                text-align: center;
                justify-content: center;
                padding: 0.7rem 1.5rem;
                width: 100%;
            }
            
            .feature-list {
                grid-template-columns: 1fr;
                gap: 0.8rem;
            }
            
            .product-badge {
                top: 12px;
                left: 12px;
                font-size: 0.6rem;
                padding: 4px 14px;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 8px;
            }
            
            .container {
                border-radius: 16px;
            }
            
            .product-image {
                height: 200px;
            }
            
            .product-info {
                padding: 1.2rem;
            }
            
            .product-title {
                font-size: 1.3rem;
            }
            
            .product-price {
                font-size: 1.1rem;
            }
            
            .product-description {
                font-size: 0.85rem;
                line-height: 1.7;
            }
            
            .breadcrumb {
                font-size: 0.65rem;
            }
            
            .breadcrumb a {
                font-size: 0.65rem;
            }
            
            .product-category {
                font-size: 0.6rem;
                padding: 0.2rem 0.8rem;
            }
            
            .btn-demo, .btn-cart, .btn-wa {
                font-size: 0.75rem;
                padding: 0.6rem 1.2rem;
            }
            
            .product-badge {
                display: none;
            }
        }
        
        /* Loading animation untuk gambar */
        .product-image {
            opacity: 0;
            transition: opacity 0.5s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Container Gambar -->
        <div class="product-image-container">
            <img src="<?= htmlspecialchars($image_src) ?>" 
                 alt="<?= htmlspecialchars($product['name']) ?>" 
                 class="product-image" 
                 onerror="this.onerror=null; this.src='https://placehold.co/800x400/1a1a2e/d4af37?text=🎨+Gambar+Tidak+Tersedia';"
                 onload="this.style.opacity='1'">
            
            <!-- Badge -->
            <div class="product-badge">
                <i class="fas fa-certificate"></i> DKV ROOM
            </div>
        </div>
        
        <div class="product-info">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="../index.php"><i class="fas fa-home"></i> Beranda</a>
                <i class="fas fa-chevron-right"></i>
                <a href="../index.php">Koleksi</a>
                <i class="fas fa-chevron-right"></i>
                <span><?= htmlspecialchars($product['name']) ?></span>
            </div>
            
            <!-- Category Badge -->
            <div class="product-category">
                <i class="fas fa-tag"></i> <?= htmlspecialchars($product['category_name']) ?>
            </div>
            
            <!-- Title -->
            <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
            
            <!-- Price -->
            <div class="product-price"><?= htmlspecialchars($product['price']) ?></div>
            
            <!-- Description -->
            <div class="product-description">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>
            
            <!-- Feature List (Opsional) -->
            <?php if (!empty($product['features'])): ?>
            <div class="feature-list">
                <?php 
                $features = explode(',', $product['features']);
                foreach ($features as $feature): 
                ?>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span><?= htmlspecialchars(trim($feature)) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Button Group -->
            <div class="button-group">
                <?php if (!empty($product['demo_link'])): ?>
                    <a href="<?= htmlspecialchars($product['demo_link']) ?>" class="btn-demo" target="_blank">
                        <i class="fas fa-desktop"></i> Lihat Demo
                    </a>
                <?php endif; ?>
                
                <!-- Tombol Simpan ke Koleksi -->
                <button class="btn-cart" 
                        id="btnSaveCollection"
                        onclick="addToCart(<?= $product['id'] ?>, '<?= addslashes(htmlspecialchars($product['name'])) ?>', '<?= addslashes(htmlspecialchars($product['price'])) ?>')">
                    <i class="fa-regular fa-star"></i> Simpan ke Koleksi
                </button>

                <a href="https://wa.me/<?= $wa_number ?>?text=Halo%2C%20saya%20tertarik%20dengan%20proyek%20<?= rawurlencode($product['name']) ?>%20di%20DKV%20ROOM.%20Mohon%20informasi%20lebih%20lanjut%20mengenai%20pengerjaan%20dan%20estimasi%20biaya.%20Terima%20kasih." 
                   class="btn-wa" target="_blank">
                    <i class="fab fa-whatsapp"></i> Konsultasi via WhatsApp
                </a>
            </div>
            
            <!-- Divider -->
            <hr class="divider">
            
            <!-- Back Button -->
            <a href="../index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Koleksi
            </a>
        </div>
    </div>
    
    <script>
        // Smooth loading untuk gambar
        const img = document.querySelector('.product-image');
        if (img) {
            if (img.complete) {
                img.style.opacity = '1';
            } else {
                img.addEventListener('load', function() {
                    this.style.opacity = '1';
                });
            }
        }
        
        // =============================================
        // ADD TO COLLECTION (AJAX)
        // =============================================
        window.addToCart = function(productId, productName, productPrice) {
            const button = document.getElementById('btnSaveCollection');
            if (button && button.disabled) {
                showToast('✨ Karya sudah tersimpan!', true);
                return;
            }
            
            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            }
            
            fetch('../ajax/cart_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `action=add&product_id=${productId}&product_name=${encodeURIComponent(productName)}&product_price=${encodeURIComponent(productPrice)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge di header
                    updateCartBadge();
                    showToast(`✨ "${productName}" tersimpan di koleksi!`);
                    
                    if (button) {
                        button.innerHTML = '<i class="fas fa-check" style="color:#d4af37;"></i> Tersimpan';
                        button.style.background = 'rgba(212,175,55,0.08)';
                        button.style.borderColor = 'rgba(212,175,55,0.15)';
                    }
                } else if (data.already_exists) {
                    showToast(`"${productName}" sudah ada di koleksi!`, true);
                    if (button) {
                        button.disabled = true;
                        button.innerHTML = '<i class="fas fa-check" style="color:#d4af37;"></i> Tersimpan';
                        button.style.background = 'rgba(212,175,55,0.08)';
                        button.style.borderColor = 'rgba(212,175,55,0.15)';
                    }
                } else {
                    showToast('Gagal menyimpan ke koleksi', true);
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = '<i class="fa-regular fa-star"></i> Simpan ke Koleksi';
                        button.style.background = 'rgba(212,175,55,0.06)';
                        button.style.borderColor = 'rgba(212,175,55,0.1)';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan sistem', true);
                if (button) {
                    button.disabled = false;
                    button.innerHTML = '<i class="fa-regular fa-star"></i> Simpan ke Koleksi';
                    button.style.background = 'rgba(212,175,55,0.06)';
                    button.style.borderColor = 'rgba(212,175,55,0.1)';
                }
            });
        };
        
        // =============================================
        // UPDATE CART BADGE
        // =============================================
        function updateCartBadge() {
            const badge = document.getElementById('cartCount');
            if (!badge) return;
            
            fetch('../ajax/cart_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'action=get'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.textContent = '0';
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error updating cart:', error));
        }
        
        // =============================================
        // TOAST NOTIFICATION
        // =============================================
        function showToast(message, isError = false) {
            const existingToast = document.querySelector('.toast-notification');
            if (existingToast) existingToast.remove();
            
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.style.background = isError ? 'rgba(239,68,68,0.9)' : 'linear-gradient(135deg, #d4af37, #b8962e)';
            toast.style.color = '#0b0b0b';
            toast.style.backdropFilter = 'blur(12px)';
            toast.style.border = '1px solid rgba(255,255,255,0.08)';
            toast.innerHTML = '<i class="fas ' + (isError ? 'fa-exclamation-circle' : 'fa-check-circle') + '"></i> ' + message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast && toast.remove) toast.remove();
            }, 3000);
        }
        
        // Cek status produk apakah sudah disimpan
        document.addEventListener('DOMContentLoaded', function() {
            const productId = <?= $product['id'] ?>;
            const button = document.getElementById('btnSaveCollection');
            
            fetch('../ajax/cart_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'action=get'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.items && data.items[productId]) {
                    if (button) {
                        button.disabled = true;
                        button.innerHTML = '<i class="fas fa-check" style="color:#d4af37;"></i> Tersimpan';
                        button.style.background = 'rgba(212,175,55,0.08)';
                        button.style.borderColor = 'rgba(212,175,55,0.15)';
                    }
                }
            })
            .catch(error => console.error('Error checking product status:', error));
        });
    </script>
</body>
</html>