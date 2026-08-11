<?php
include '../config.php';
include '../includes/cart_functions.php';

// Ambil semua item dari cart
$cart_items = getCartItems();
$total_price = getCartTotal();

// Gunakan fungsi get_wa_number() yang aman
$wa_default = get_wa_number();

// Buat pesan rapi untuk WhatsApp
$wa_message = "Halo DKV ROOM,\n\nSaya berminat untuk berkonsultasi mengenai koleksi karya yang telah saya simpan berikut:\n\n";
$no = 1;
foreach ($cart_items as $item) {
    $wa_message .= $no . ". *" . $item['name'] . "* - " . format_rupiah($item['price']) . "\n";
    $no++;
}
$wa_message .= "\n*Total Estimasi Biaya:* " . format_rupiah($total_price) . "\n\n";
$wa_message .= "Mohon informasi lebih lanjut mengenai pengerjaan dan ketersediaan slot. Terima kasih!";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Koleksi Simpanan — DKV ROOM</title>
    
    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: 
                radial-gradient(ellipse at 20% 50%, rgba(212, 175, 55, 0.05) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 50%, rgba(212, 175, 55, 0.03) 0%, transparent 50%),
                linear-gradient(135deg, #0b0b0b 0%, #161616 50%, #1a1a1a 100%);
            color: #f5f5f5;
            min-height: 100vh;
            padding: 2rem 1rem;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.04), transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(20, 20, 20, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 28px;
            border: 1px solid rgba(212, 175, 55, 0.15);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.6);
            overflow: hidden;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .cart-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            background: rgba(11, 11, 11, 0.5);
        }

        .cart-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: #f5f5f5;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cart-header h1 i {
            color: #d4af37;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.04);
            color: rgba(212, 175, 55, 0.85);
            border: 1px solid rgba(212, 175, 55, 0.2);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: rgba(212, 175, 55, 0.1);
            color: #d4af37;
            border-color: rgba(212, 175, 55, 0.4);
            transform: translateY(-1px);
        }

        /* Empty State */
        .cart-empty {
            text-align: center;
            padding: 4rem 2rem;
        }

        .cart-empty .empty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.08);
            border: 1px solid rgba(212, 175, 55, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #d4af37;
            font-size: 2rem;
            box-shadow: 0 0 30px rgba(212, 175, 55, 0.1);
        }

        .cart-empty h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
            color: #f5f5f5;
        }

        .cart-empty p {
            color: #a0a0a0;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            max-width: 420px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        .btn-shop {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #d4af37, #b8962e);
            color: #0b0b0b;
            padding: 0.75rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.25);
        }

        .btn-shop:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(212, 175, 55, 0.4);
            background: linear-gradient(135deg, #e0c040, #c4a032);
        }

        /* Cart Items List */
        .cart-items {
            padding: 1.5rem 2rem;
        }

        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.2rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            gap: 1rem;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-name {
            font-weight: 700;
            color: #f5f5f5;
            font-size: 1.05rem;
            margin-bottom: 0.3rem;
        }

        .cart-item-category {
            font-size: 0.7rem;
            color: #d4af37;
            background: rgba(212, 175, 55, 0.08);
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }

        .cart-item-price {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: #d4af37;
            font-size: 1.1rem;
            white-space: nowrap;
        }

        .cart-item-remove {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
            cursor: pointer;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .cart-item-remove:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            transform: scale(1.05);
        }

        .cart-summary {
            background: rgba(11, 11, 11, 0.6);
            padding: 1.5rem 2rem;
            border-top: 1px solid rgba(212, 175, 55, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .cart-total {
            font-size: 0.95rem;
            color: #a0a0a0;
        }

        .cart-total span {
            font-family: 'Playfair Display', serif;
            font-weight: 800;
            color: #d4af37;
            font-size: 1.5rem;
            margin-left: 0.5rem;
        }

        .btn-checkout {
            background: linear-gradient(135deg, #25D366, #1da85e);
            color: #0b0b0b;
            padding: 0.75rem 1.8rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.25);
        }

        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4);
            background: linear-gradient(135deg, #28e06f, #1da85e);
        }

        .btn-clear {
            background: transparent;
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 0.65rem 1.2rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-clear:hover {
            background: rgba(239, 68, 68, 0.15);
            border-color: #ef4444;
            color: #ef4444;
        }

        @media (max-width: 600px) {
            .cart-header, .cart-items, .cart-summary {
                padding: 1.2rem 1.2rem;
            }
            .cart-summary {
                flex-direction: column;
                align-items: stretch;
            }
            .btn-checkout, .btn-clear {
                justify-content: center;
            }
            .cart-total {
                text-align: center;
            }
        }

        /* Toast notification */
        .toast-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #d4af37;
            color: #0b0b0b;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            z-index: 1000;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(12px);
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="cart-header">
            <h1><i class="fa-regular fa-star"></i> Koleksi Simpanan Saya</h1>
            <a href="../index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Katalog</a>
        </div>
        
        <?php if (empty($cart_items)): ?>
            <div class="cart-empty">
                <div class="empty-icon">
                    <i class="fa-regular fa-star"></i>
                </div>
                <h3>Belum Ada Karya yang Disimpan</h3>
                <p>Jelajahi katalog DKV ROOM dan simpan karya favorit Anda untuk didiskusikan atau dikonsultasikan bersama tim kami.</p>
                <a href="../index.php" class="btn-shop"><i class="fas fa-palette"></i> Lihat Katalog Karya</a>
            </div>
        <?php else: ?>
            <div class="cart-items" id="cartItemsContainer">
                <?php foreach ($cart_items as $id => $item): ?>
                <div class="cart-item" data-id="<?= $id ?>">
                    <div class="cart-item-info">
                        <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
                        <span class="cart-item-category"><i class="fas fa-gem"></i> Koleksi DKV</span>
                    </div>
                    <div class="cart-item-price">
                        <?= htmlspecialchars(format_rupiah($item['price'])) ?>
                    </div>
                    <button class="cart-item-remove" onclick="removeFromCart(<?= $id ?>)" title="Hapus dari simpanan">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <button class="btn-clear" onclick="clearCart()">
                    <i class="fas fa-trash-alt"></i> Kosongkan Simpanan
                </button>
                <div class="cart-total">
                    Total Estimasi: <span><?= format_rupiah($total_price) ?></span>
                </div>
                <button type="button" onclick="checkoutViaWA()" class="btn-checkout" id="btnCheckoutWA">
                    <i class="fab fa-whatsapp"></i> Konsultasi Semua via WA
                </button>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        function checkoutViaWA() {
            const btn = document.getElementById('btnCheckoutWA');
            if (btn) btn.disabled = true;
            
            showToast('Memproses invoice & mengalihkan ke WhatsApp...');
            
            fetch('../ajax/cart_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'action=checkout'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.open(data.wa_url, '_blank');
                    
                    if (window.opener && window.opener.updateCartBadge) {
                        window.opener.updateCartBadge();
                    }
                    
                    setTimeout(() => {
                        location.reload();
                    }, 800);
                } else {
                    showToast(data.message || 'Gagal memproses checkout', true);
                    if (btn) btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan sistem', true);
                if (btn) btn.disabled = false;
            });
        }
        function showToast(message, isError = false) {
            const existingToast = document.querySelector('.toast-notification');
            if (existingToast) existingToast.remove();
            
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.style.background = isError ? '#ef4444' : '#d4af37';
            toast.style.color = isError ? '#ffffff' : '#0b0b0b';
            toast.innerHTML = '<i class="fas ' + (isError ? 'fa-exclamation-circle' : 'fa-check-circle') + '"></i> ' + message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast && toast.remove) toast.remove();
            }, 2500);
        }
        
        function updateCartDisplay() {
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
                    if (window.opener && window.opener.updateCartBadge) {
                        window.opener.updateCartBadge();
                    }
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        function removeFromCart(productId) {
            if (confirm('Hapus karya ini dari koleksi simpanan?')) {
                fetch('../ajax/cart_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `action=remove&product_id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Karya dihapus dari simpanan');
                        updateCartDisplay();
                    } else {
                        showToast('Gagal menghapus karya', true);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan', true);
                });
            }
        }
        
        function clearCart() {
            if (confirm('Kosongkan semua karya simpanan?')) {
                fetch('../ajax/cart_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'action=clear'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Koleksi simpanan berhasil dikosongkan');
                        updateCartDisplay();
                    } else {
                        showToast('Gagal mengosongkan simpanan', true);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan', true);
                });
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            if (window.opener && window.opener.updateCartBadge) {
                window.opener.updateCartBadge();
            }
        });
    </script>
</body>
</html>