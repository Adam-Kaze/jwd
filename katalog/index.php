<?php
include 'config.php';

/** @var mysqli $conn */
global $conn;

// --- GUNAKAN FUNGSI HELPER DARI CONFIG.PHP ---
$wa_default = get_wa_number(); // Ambil nomor WA
$products   = get_all_products(); // Ambil semua produk
$categories = get_categories(); // Ambil kategori

// --- AMBIL JUMLAH CART SAAT INI (UNTUK BADGE) ---
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    $cart_count = count($_SESSION['cart']);
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <div class="products-header">
        <h2><i class="fa-regular fa-gem" style="color: #d4af37; margin-right: 10px;"></i> Koleksi Kreatif DKV ROOM</h2>
        <p>Desain grafis · Ilustrasi · Fotografi · Produksi video — untuk branding &amp; komunikasi visual Anda</p>
    </div>
    
    <?php if (empty($products)): ?>
        <div class="empty-state">
            <i class="fa-regular fa-pen-nib"></i>
            <h3>Portofolio Segera Hadir</h3>
            <p>Kami sedang menyiapkan karya-karya terbaik untuk Anda.</p>
            <p>Untuk konsultasi desain &amp; produksi visual, hubungi tim kami.</p>
            <a href="https://wa.me/<?= $wa_default ?>" class="btn-wa" target="_blank">
                <i class="fab fa-whatsapp"></i> Hubungi Studio
            </a>
        </div>
    <?php else: ?>
        <div class="products-grid" id="productsGrid">
            <?php foreach ($products as $product): ?>
                <div class="product-card" data-category="<?= htmlspecialchars($product['category_name']) ?>" data-product-id="<?= $product['id'] ?>">
                    <div class="product-img-wrapper">
                        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img"
                             onerror="this.src='https://placehold.co/400x200/d4af37/0b0b0b?text=✨<?= rawurlencode(substr($product['name'], 0, 20)) ?>'">
                        <span class="product-badge">Koleksi DKV</span>
                    </div>
                    <div class="product-body">
                        <span class="product-category"><?= htmlspecialchars($product['category_name']) ?></span>
                        <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-desc"><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                        <div class="product-price"><?= htmlspecialchars($product['price']) ?></div>
                        <div class="product-buttons">
                            <a href="detail/index.php?id=<?= $product['id'] ?>" class="btn-detail">
                                <i class="fa-regular fa-eye"></i> Detail
                            </a>
                            
                            <!-- TOMBOL SIMPAN -->
                            <button class="btn-cart" 
                                    onclick="addToCart(<?= $product['id'] ?>, '<?= addslashes(htmlspecialchars($product['name'])) ?>', '<?= addslashes(htmlspecialchars($product['price'])) ?>')">
                                <i class="fa-regular fa-star"></i> Simpan
                            </button>
                            
                            <a href="<?= get_product_wa_consultation_link($product, $wa_default) ?>" 
                               class="btn-wa" target="_blank">
                                <i class="fab fa-whatsapp"></i> Konsultasi
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>

<!-- ============================================================ -->
<!-- JAVASCRIPT CART (DITEMPATKAN DI SINI AGAR PASTI TERBACA)     -->
<!-- ============================================================ -->
<script>
    // =============================================
    // 1. FUNGSI ADD TO CART (Menggunakan AJAX)
    // =============================================
    window.addToCart = function(productId, productName, productPrice) {
        // Jika tombol disable, jangan lakukan apa-apa
        const button = event ? event.currentTarget : document.querySelector(`.btn-cart[onclick*="addToCart(${productId},"]`);
        if (button && button.disabled) {
            showToast('Karya sudah disimpan!', true);
            return;
        }

        // Ubah status tombol menjadi loading
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fa-regular fa-spinner fa-spin"></i> Menyimpan...';
        }

        // Kirim request AJAX ke file cart_functions (bukan cart.php)
        fetch('ajax/cart_ajax.php', {
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
                const badge = document.getElementById('cartCount');
                if (badge) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                }
                showToast(`${productName} tersimpan di koleksi!`);
                
                // Ubah status tombol menjadi "Tersimpan"
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<i class="fa-regular fa-star" style="color: #d4af37;"></i> Tersimpan';
                    button.style.background = '#1a1a1a';
                    button.style.color = '#d4af37';
                    button.style.borderColor = '#d4af37';
                }
            } else if (data.already_exists) {
                showToast(`${productName} sudah ada di koleksi!`, true);
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<i class="fa-regular fa-star" style="color: #d4af37;"></i> Tersimpan';
                }
            } else {
                showToast('Gagal menyimpan ke koleksi', true);
                if (button) {
                    button.disabled = false;
                    button.innerHTML = '<i class="fa-regular fa-star"></i> Simpan';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan sistem', true);
            if (button) {
                button.disabled = false;
                button.innerHTML = '<i class="fa-regular fa-star"></i> Simpan';
            }
        });
    };

    // =============================================
    // 2. FUNGSI TOAST NOTIFICATION
    // =============================================
    function showToast(message, isError = false) {
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) existingToast.remove();
        
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.style.background = isError ? '#dc2626' : '#d4af37';
        toast.style.boxShadow = '0 12px 40px rgba(212, 175, 55, .30)';
        toast.innerHTML = '<i class="fa-regular ' + (isError ? 'fa-circle-xmark' : 'fa-circle-check') + '" style="color: #0b0b0b;"></i> ' + message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast && toast.remove) toast.remove();
        }, 2500);
    }
</script>

<!-- ============================================================ -->
<!-- TAMBAHAN STYLE UNTUK DKV ROOM KATALOG                        -->
<!-- ============================================================ -->
<style>
    /* ==========================================
       OVERRIDE KATALOG - DKV ROOM STYLE
       ========================================== */
    
    /* ===== BACKGROUND GRADIENT (REVISI) ===== */
    body {
        background: 
            radial-gradient(ellipse at 20% 50%, rgba(212, 175, 55, 0.05) 0%, transparent 60%),
            radial-gradient(ellipse at 80% 50%, rgba(212, 175, 55, 0.03) 0%, transparent 50%),
            linear-gradient(135deg, #0b0b0b 0%, #161616 50%, #1a1a1a 100%);
        min-height: 100vh;
        position: relative;
    }

    /* Tambahan efek glow seperti di halaman utama */
    body::before {
        content: "";
        position: fixed;
        width: 600px;
        height: 600px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(212, 175, 55, 0.04), transparent 70%);
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        filter: blur(80px);
        animation: pulseGlow 8s ease-in-out infinite;
        pointer-events: none;
        z-index: 0;
    }

    @keyframes pulseGlow {
        0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.4; }
        50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.8; }
    }

    /* Main content harus di atas background */
    .main-content {
        position: relative;
        z-index: 1;
    }

    /* Header */
    .products-header {
        position: relative;
        z-index: 2;
    }

    .products-header h2 {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        color: #f5f5f5;
        font-size: 28px;
        letter-spacing: -0.5px;
    }

    .products-header p {
        color: #b0a0c0;
        font-weight: 400;
        font-size: 15px;
    }

    /* Card produk - Dark theme dengan aksen emas */
    .product-card {
        border-radius: 20px;
        background: rgba(20, 20, 20, 0.85);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(212, 175, 55, 0.15);
        box-shadow: 0 12px 30px rgba(0, 0, 0, .4);
        transition: all 0.4s ease;
        overflow: hidden;
        position: relative;
        z-index: 2;
    }

    .product-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 50px rgba(212, 175, 55, .12);
        border-color: rgba(212, 175, 55, .35);
    }

    .product-img-wrapper {
        position: relative;
        overflow: hidden;
        background: #0b0b0b;
    }

    .product-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        transition: transform 0.5s ease;
        filter: brightness(0.85);
    }

    .product-card:hover .product-img {
        transform: scale(1.03);
        filter: brightness(1);
    }

    .product-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: linear-gradient(135deg, #d4af37, #f5d77b);
        color: #0b0b0b;
        font-size: 10px;
        font-weight: 700;
        padding: 4px 14px;
        border-radius: 40px;
        letter-spacing: 0.3px;
        box-shadow: 0 4px 12px rgba(212, 175, 55, .25);
    }

    .product-body {
        padding: 18px 20px 22px;
        background: #0f0f0f;
    }

    .product-category {
        display: inline-block;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #d4af37;
        background: rgba(212, 175, 55, .10);
        padding: 3px 14px;
        border-radius: 40px;
        margin-bottom: 8px;
        border: 1px solid rgba(212, 175, 55, .08);
    }

    .product-title {
        font-size: 17px;
        font-weight: 700;
        color: #f5f5f5;
        margin-bottom: 6px;
        line-height: 1.3;
    }

    .product-desc {
        font-size: 13px;
        color: #a0a0a0;
        line-height: 1.6;
        margin-bottom: 12px;
    }

    .product-price {
        font-size: 18px;
        font-weight: 700;
        color: #d4af37;
        margin-bottom: 14px;
        font-family: 'Playfair Display', serif;
    }

    .product-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .product-buttons a,
    .product-buttons button {
        flex: 1;
        min-width: 80px;
        justify-content: center;
        padding: 10px 14px;
        border-radius: 40px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-detail {
        background: rgba(255, 255, 255, 0.06);
        color: #d4af37;
        border: 1px solid rgba(212, 175, 55, 0.15);
    }

    .btn-detail:hover {
        background: rgba(212, 175, 55, 0.12);
        transform: translateY(-2px);
        border-color: rgba(212, 175, 55, 0.3);
    }

    .btn-cart {
        background: rgba(255, 255, 255, 0.04);
        color: #e0e0e0;
        border: 2px solid rgba(255, 255, 255, 0.08);
    }

    .btn-cart:hover:not(:disabled) {
        background: rgba(212, 175, 55, 0.08);
        border-color: #d4af37;
        color: #d4af37;
        transform: translateY(-2px);
    }

    .btn-cart:disabled {
        opacity: 0.8;
        cursor: default;
        transform: none !important;
    }

    .btn-wa {
        background: #25D366;
        color: #0b0b0b;
        border: none;
        font-weight: 700;
    }

    .btn-wa:hover {
        background: #1da851;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 211, 102, .30);
        color: #0b0b0b;
    }

    /* Empty state - Dark theme */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: rgba(20, 20, 20, 0.7);
        backdrop-filter: blur(8px);
        border-radius: 32px;
        border: 1px solid rgba(212, 175, 55, 0.1);
        position: relative;
        z-index: 2;
    }

    .empty-state i {
        font-size: 56px;
        color: #d4af37;
        margin-bottom: 16px;
        display: block;
    }

    .empty-state h3 {
        font-size: 22px;
        color: #f5f5f5;
        font-family: 'Playfair Display', serif;
        margin-bottom: 8px;
    }

    .empty-state p {
        color: #a0a0a0;
        margin-bottom: 6px;
    }

    .empty-state .btn-wa {
        display: inline-flex;
        margin-top: 16px;
        padding: 12px 32px;
        border-radius: 40px;
        background: #25D366;
        color: #0b0b0b;
        font-weight: 700;
        text-decoration: none;
        align-items: center;
        gap: 8px;
        transition: 0.3s;
    }

    .empty-state .btn-wa:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(37, 211, 102, .30);
    }

    /* Toast notification - Dark theme */
    .toast-notification {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        padding: 14px 28px;
        border-radius: 60px;
        color: #0b0b0b;
        font-weight: 700;
        font-size: 14px;
        z-index: 9999;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideUp 0.4s ease;
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }

    /* Sidebar dan header tetap konsisten */
    .sidebar {
        z-index: 2;
        background: rgba(11, 11, 11, 0.9);
        backdrop-filter: blur(10px);
        border-right: 1px solid rgba(212, 175, 55, 0.08);
    }

    .header {
        position: relative;
        z-index: 2;
        background: rgba(11, 11, 11, 0.9);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(212, 175, 55, 0.08);
    }

    /* Responsif */
    @media (max-width: 768px) {
        .products-header h2 {
            font-size: 22px;
        }

        .product-body {
            padding: 14px 16px 18px;
        }

        .product-title {
            font-size: 15px;
        }

        .product-buttons a,
        .product-buttons button {
            font-size: 11px;
            padding: 8px 12px;
            min-width: 60px;
        }

        .toast-notification {
            font-size: 12px;
            padding: 12px 20px;
            bottom: 16px;
            width: 90%;
        }

        body::before {
            width: 300px;
            height: 300px;
            filter: blur(60px);
        }
    }

    /* Grid produk - pastikan tetap rapi */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
        padding: 10px 0;
        position: relative;
        z-index: 2;
    }

    /* Animasi tambahan untuk kesan premium */
    .product-card {
        animation: fadeInUp 0.6s ease both;
    }

    .product-card:nth-child(2) { animation-delay: 0.1s; }
    .product-card:nth-child(3) { animation-delay: 0.2s; }
    .product-card:nth-child(4) { animation-delay: 0.3s; }
    .product-card:nth-child(5) { animation-delay: 0.4s; }
    .product-card:nth-child(6) { animation-delay: 0.5s; }

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
</style>