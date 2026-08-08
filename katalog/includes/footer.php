<?php
// ============================================================
// FOOTER - DKV ROOM (DARK GOLD THEME)
// ============================================================

/** @var mysqli $conn */
global $conn;

// --- DATA DEFAULTS (FALLBACK JIKA DATABASE KOSONG) ---
$footer_copyright   = 'DKV ROOM – Desain, Ilustrasi, Foto & Video';
$footer_credit_left = '<i class="fas fa-mobile-alt"></i> Mobile Friendly';
$footer_credit_right = '<i class="fas fa-tachometer-alt"></i> Kinerja Cepat';
$wa_default_promo   = '6281383796300';

// --- DATA DEFAULT UNTUK CTA (SMART PROMO) ---
$cta_defaults = [
    'cta_title'       => '💡 Konsultasi Gratis!',
    'cta_message'     => 'Butuh bantuan untuk proyek desain, ilustrasi, atau produksi video? Tim DKV ROOM siap membantu!',
    'cta_emoji'       => '🎨',
    'cta_btn_text'    => 'Hubungi Sekarang →',
    'cta_wa_text'     => 'Halo, saya butuh konsultasi untuk proyek kreatif di DKV ROOM.',
    'cta_footer_text' => '⭐ 100+ klien puas | ⚡ Respon cepat | 🎯 Kualitas terbaik'
];

$cta_data = $cta_defaults;

// --- AMBIL DATA DARI DATABASE (AMAN DENGAN PREPARED STATEMENT) ---
if (isset($conn)) {
    // 1. Ambil data Footer & WA
    $stmt_footer = mysqli_prepare($conn, "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('footer_copyright', 'footer_credit_left', 'footer_credit_right', 'wa_number')");
    mysqli_stmt_execute($stmt_footer);
    $result_footer = mysqli_stmt_get_result($stmt_footer);
    
    while ($row = mysqli_fetch_assoc($result_footer)) {
        switch ($row['setting_key']) {
            case 'footer_copyright':  $footer_copyright = $row['setting_value']; break;
            case 'footer_credit_left': $footer_credit_left = $row['setting_value']; break;
            case 'footer_credit_right': $footer_credit_right = $row['setting_value']; break;
            case 'wa_number': $wa_default_promo = $row['setting_value']; break;
        }
    }
    mysqli_stmt_close($stmt_footer);

    // 2. Ambil data CTA (Smart Promo)
    $keys_cta = array_keys($cta_defaults);
    $placeholders = implode(',', array_fill(0, count($keys_cta), '?'));
    $types = str_repeat('s', count($keys_cta));

    $stmt_cta = mysqli_prepare($conn, "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ($placeholders)");
    mysqli_stmt_bind_param($stmt_cta, $types, ...$keys_cta);
    mysqli_stmt_execute($stmt_cta);
    $result_cta = mysqli_stmt_get_result($stmt_cta);

    while ($row = mysqli_fetch_assoc($result_cta)) {
        $cta_data[$row['setting_key']] = $row['setting_value'];
    }
    mysqli_stmt_close($stmt_cta);
}
?>
    </div> <!-- .layout -->
    
    <!-- ==========================================
    FOOTER - DARK GOLD THEME
    ========================================== -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-left">
                <p>&copy; <?= date('Y') ?> <strong style="color: #d4af37;">DKV ROOM</strong></p>
                <p class="footer-copyright"><?= htmlspecialchars($footer_copyright) ?></p>
            </div>
            <div class="footer-right">
                <span class="footer-credit"><?= $footer_credit_left ?></span>
                <span class="footer-divider">|</span>
                <span class="footer-credit"><?= $footer_credit_right ?></span>
            </div>
        </div>
    </footer>
</div> <!-- .app -->

<script>
    // =============================================
    // 1. TOGGLE SIDEBAR MOBILE
    // =============================================
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('active');
        });
        
        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            });
        }
    }

    // =============================================
    // 2. SEARCH TOGGLE & AJAX
    // =============================================
    const searchIcon = document.getElementById('searchIcon');
    const searchInput = document.getElementById('searchInput');
    const searchKeyword = document.getElementById('searchKeyword');
    const searchResults = document.getElementById('searchResults');

    if (searchIcon && searchInput) {
        searchIcon.addEventListener('click', function() {
            searchInput.classList.toggle('active');
            if (searchInput.classList.contains('active')) {
                searchKeyword.focus();
            }
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && e.target !== searchIcon) {
                searchInput.classList.remove('active');
            }
        });
    }

    if (searchKeyword && searchResults) {
        let debounceTimer;
        searchKeyword.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const keyword = this.value.trim();
            
            if (keyword.length < 2) {
                searchResults.innerHTML = '';
                return;
            }

            if (keyword.length >= 3) {
                trackUserBehavior('search', keyword);
            }

            debounceTimer = setTimeout(() => {
                fetch('/katalog/search.php?q=' + encodeURIComponent(keyword))
                .then(response => response.json())
                .then(data => {
                    if (data.results.length > 0) {
                        let html = '';
                        data.results.forEach(item => {
                            html += `
                                <a href="/katalog/detail/index.php?id=${item.id}" class="search-result-item" onclick="trackUserBehavior('view', '${item.name.replace(/'/g, "\\'")}')">
                                    <img src="${item.image_url}" alt="${item.name}" class="search-result-img">
                                    <div class="search-result-info">
                                        <div class="search-result-name">${escapeHtml(item.name)}</div>
                                        <div class="search-result-price">${item.price}</div>
                                    </div>
                                </a>
                            `;
                        });
                        searchResults.innerHTML = html;
                    } else {
                        searchResults.innerHTML = '<div class="search-no-result">Produk tidak ditemukan.</div>';
                    }
                })
                .catch(error => console.error('Error:', error));
            }, 300);
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // =============================================
    // 3. CART BADGE UPDATE & BUTTON STATUS
    // =============================================
    function updateCartBadge() {
        const badge = document.getElementById('cartCount');
        if (!badge) return;

        fetch('/katalog/ajax/cart_ajax.php', {
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

    function updateButtonStatus() {
        fetch('/katalog/ajax/cart_ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'action=get'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.items) {
                const cartItemIds = Object.keys(data.items);
                
                document.querySelectorAll('.btn-cart').forEach(button => {
                    const onclickAttr = button.getAttribute('onclick');
                    let productId = null;
                    
                    if (onclickAttr) {
                        const match = onclickAttr.match(/addToCart\((\d+),/);
                        if (match) productId = match[1];
                    }
                    
                    if (productId && cartItemIds.includes(productId)) {
                        button.innerHTML = '<i class="fas fa-check"></i> Tersimpan';
                        button.disabled = true;
                        button.style.background = 'rgba(212, 175, 55, 0.08)';
                        button.style.color = '#d4af37';
                        button.style.borderColor = 'rgba(212, 175, 55, 0.2)';
                        button.style.cursor = 'not-allowed';
                    } else {
                        button.innerHTML = '<i class="fas fa-star"></i> Simpan';
                        button.disabled = false;
                        button.style.background = 'rgba(255,255,255,0.04)';
                        button.style.color = '#e0e0e0';
                        button.style.borderColor = 'rgba(255,255,255,0.08)';
                        button.style.cursor = 'pointer';
                    }
                });
            }
        })
        .catch(error => console.error('Error update button status:', error));
    }

    // =============================================
    // 4. ADD TO CART (Fungsi Global)
    // =============================================
    window.addToCart = function(productId, productName, productPrice) {
        const button = event ? event.currentTarget : document.querySelector(`.btn-cart[onclick*="addToCart(${productId},"]`);
        if (button && button.disabled) {
            showToast('Produk sudah disimpan!', true);
            return;
        }
        
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        }
        
        trackUserBehavior('save', productName);
        
        fetch('/katalog/ajax/cart_ajax.php', {
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
                updateCartBadge();
                showToast(`${productName} tersimpan di koleksi!`);
                updateButtonStatus();
                if (!promoTriggered) {
                    promoTriggered = true;
                    setTimeout(() => showSmartPromo(), 1000);
                }
            } else if (data.already_exists) {
                showToast(`${productName} sudah ada di koleksi!`, true);
                updateButtonStatus();
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-check"></i> Tersimpan';
                }
            } else {
                showToast('Gagal menyimpan ke koleksi', true);
                if (button) {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-star"></i> Simpan';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan sistem', true);
            if (button) {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-star"></i> Simpan';
            }
        });
    };

    // =============================================
    // 5. TOAST NOTIFICATION
    // =============================================
    function showToast(message, isError = false) {
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) existingToast.remove();
        
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.style.background = isError ? '#dc2626' : '#d4af37';
        toast.style.color = '#0b0b0b';
        toast.innerHTML = '<i class="fas ' + (isError ? 'fa-exclamation-circle' : 'fa-check-circle') + '"></i> ' + message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast && toast.remove) toast.remove();
        }, 2500);
    }

    // =============================================
    // 6. SMART PROMO FLOATING CARD
    // =============================================
    
    let userBehavior = {
        lastViewedProduct: null,
        searchedKeywords: [],
        savedProducts: [],
        lastActivity: null
    };
    
    if (sessionStorage.getItem('userBehavior')) {
        try {
            userBehavior = JSON.parse(sessionStorage.getItem('userBehavior'));
        } catch(e) {}
    }
    
    window.trackUserBehavior = function(type, data) {
        switch(type) {
            case 'view':
                userBehavior.lastViewedProduct = data;
                userBehavior.lastActivity = new Date().toISOString();
                break;
            case 'search':
                if (!userBehavior.searchedKeywords.includes(data)) {
                    userBehavior.searchedKeywords.unshift(data);
                    userBehavior.searchedKeywords = userBehavior.searchedKeywords.slice(0, 5);
                }
                userBehavior.lastActivity = new Date().toISOString();
                break;
            case 'save':
                if (!userBehavior.savedProducts.includes(data)) {
                    userBehavior.savedProducts.push(data);
                }
                userBehavior.lastActivity = new Date().toISOString();
                break;
        }
        sessionStorage.setItem('userBehavior', JSON.stringify(userBehavior));
        triggerPromoAfterBehavior();
    };
    
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            const productName = this.querySelector('.product-title')?.textContent;
            if (productName && !userBehavior.lastViewedProduct) {
                trackUserBehavior('view', productName);
            }
        });
    });
    
    function getSmartPromoMessage() {
        if (userBehavior.savedProducts.length > 0) {
            const savedProduct = userBehavior.savedProducts[userBehavior.savedProducts.length - 1];
            return {
                title: "🎯 Karya Tersimpan!",
                message: `"${savedProduct.substring(0, 35)}" siap untuk Anda. Konsultasikan sekarang untuk realisasi!`,
                emoji: "💎",
                cta: "Konsultasi Sekarang →",
                waMessage: `Halo, saya sudah menyimpan "${savedProduct}" dan ingin mendiskusikan pengerjaannya.`
            };
        }
        
        if (userBehavior.lastViewedProduct) {
            const viewedProduct = userBehavior.lastViewedProduct;
            return {
                title: "👀 Tertarik dengan Karya Ini?",
                message: `"${viewedProduct.substring(0, 35)}" — dapatkan penawaran spesial untuk pengerjaan desain dan produksi!`,
                emoji: "✨",
                cta: "Ambil Penawaran →",
                waMessage: `Halo, saya tertarik dengan "${viewedProduct}". Apakah ada promo spesial saat ini?`
            };
        }
        
        if (userBehavior.searchedKeywords.length >= 2) {
            const lastSearch = userBehavior.searchedKeywords[0];
            return {
                title: "🔍 Temukan Kebutuhan Visual Anda",
                message: `Anda mencari "${lastSearch}". Kami punya portofolio terbaik untuk kebutuhan desain & produksi video.`,
                emoji: "🚀",
                cta: "Tanya Rekomendasi →",
                waMessage: `Halo, saya sedang mencari "${lastSearch}". Bantu rekomendasikan solusi terbaik dong.`
            };
        }
        
        return {
            title: "<?= htmlspecialchars($cta_data['cta_title']) ?>",
            message: "<?= htmlspecialchars($cta_data['cta_message']) ?>",
            emoji: "<?= htmlspecialchars($cta_data['cta_emoji']) ?>",
            cta: "<?= htmlspecialchars($cta_data['cta_btn_text']) ?>",
            waMessage: "<?= htmlspecialchars($cta_data['cta_wa_text']) ?>"
        };
    }
    
    let promoCard = null;
    let promoTimeout = null;
    let promoTriggered = false;
    let behaviorTimeout = null;
    
    function showSmartPromo() {
        if (promoCard) return;
        
        const promo = getSmartPromoMessage();
        const waNumber = '<?= $wa_default_promo ?>';
        const footerText = '<?= htmlspecialchars($cta_data['cta_footer_text']) ?>';
        
        promoCard = document.createElement('div');
        promoCard.className = 'smart-promo-card';
        promoCard.innerHTML = `
            <div class="smart-promo-header">
                <span class="smart-promo-emoji">${promo.emoji}</span>
                <span class="smart-promo-title">${promo.title}</span>
                <button class="smart-promo-close">&times;</button>
            </div>
            <div class="smart-promo-body">
                <p>${promo.message}</p>
                <a href="https://wa.me/${waNumber}?text=${encodeURIComponent(promo.waMessage)}" 
                   class="smart-promo-btn" target="_blank">
                    ${promo.cta} <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="smart-promo-footer">
                ${footerText}
            </div>
        `;
        
        document.body.appendChild(promoCard);
        
        const closeBtn = promoCard.querySelector('.smart-promo-close');
        closeBtn.addEventListener('click', () => {
            promoCard.remove();
            promoCard = null;
            localStorage.setItem('promoLastClosed', Date.now());
        });
        
        promoTimeout = setTimeout(() => {
            if (promoCard) {
                promoCard.style.animation = 'slideUp 0.3s reverse';
                setTimeout(() => {
                    if (promoCard) {
                        promoCard.remove();
                        promoCard = null;
                    }
                }, 300);
            }
        }, 20000);
    }
    
    function shouldShowPromo() {
        const lastClosed = localStorage.getItem('promoLastClosed');
        if (lastClosed && (Date.now() - lastClosed) < 30 * 60 * 1000) {
            return false;
        }
        return true;
    }
    
    function triggerPromoAfterBehavior() {
        if (promoTriggered) return;
        if (behaviorTimeout) clearTimeout(behaviorTimeout);
        
        behaviorTimeout = setTimeout(() => {
            if (shouldShowPromo() && !promoTriggered) {
                promoTriggered = true;
                showSmartPromo();
            }
        }, 3000);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        updateCartBadge();
        updateButtonStatus();
        
        setTimeout(() => {
            if (shouldShowPromo() && !promoTriggered && 
                (userBehavior.lastViewedProduct || userBehavior.searchedKeywords.length > 0)) {
                promoTriggered = true;
                showSmartPromo();
            }
        }, 5000);
        
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            if (!promoTriggered && window.scrollY > 400) {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    if (!promoTriggered && shouldShowPromo()) {
                        promoTriggered = true;
                        showSmartPromo();
                    }
                }, 1500);
            }
        });
    });

    // =============================================
    // 7. CSS TAMBAHAN (DARK GOLD THEME)
    // =============================================
    if (!document.querySelector('#footer-extras-style')) {
        const style = document.createElement('style');
        style.id = 'footer-extras-style';
        style.textContent = `
            /* ==========================================
               MAIN FOOTER - DARK GOLD THEME
               ========================================== */
            .main-footer {
                background: rgba(15, 15, 15, 0.8);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-top: 1px solid rgba(212, 175, 55, 0.06);
                padding: 1.2rem 2rem;
                margin-top: 2rem;
            }
            
            .footer-content {
                max-width: 1300px;
                margin: 0 auto;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 0.8rem;
            }
            
            .footer-left {
                display: flex;
                align-items: center;
                gap: 0.8rem;
                flex-wrap: wrap;
            }
            
            .footer-left p {
                font-size: 0.75rem;
                color: rgba(255, 255, 255, 0.25);
                margin: 0;
            }
            
            .footer-left p strong {
                font-weight: 700;
            }
            
            .footer-copyright {
                color: rgba(255, 255, 255, 0.15) !important;
                font-size: 0.7rem !important;
            }
            
            .footer-right {
                display: flex;
                align-items: center;
                gap: 0.6rem;
                flex-wrap: wrap;
            }
            
            .footer-credit {
                font-size: 0.7rem;
                color: rgba(255, 255, 255, 0.2);
            }
            
            .footer-credit i {
                color: #d4af37;
                margin-right: 4px;
            }
            
            .footer-divider {
                color: rgba(255, 255, 255, 0.05);
                font-size: 0.7rem;
            }
            
            /* ==========================================
               SMART PROMO CARD - DARK GOLD THEME
               ========================================== */
            .smart-promo-card {
                position: fixed;
                bottom: 30px;
                left: 30px;
                width: 360px;
                max-width: calc(100vw - 60px);
                background: linear-gradient(160deg, #131313 0%, #1a1a1a 45%, #222 100%);
                border: 1px solid rgba(212, 175, 55, 0.15);
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
                z-index: 1001;
                animation: slideUp 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
                overflow: hidden;
            }
            
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(50px) scale(0.95); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }
            
            .smart-promo-header {
                background: linear-gradient(135deg, rgba(212, 175, 55, 0.08), rgba(212, 175, 55, 0.02));
                padding: 12px 16px;
                display: flex;
                align-items: center;
                gap: 10px;
                border-bottom: 1px solid rgba(212, 175, 55, 0.06);
            }
            
            .smart-promo-emoji {
                font-size: 1.3rem;
            }
            
            .smart-promo-title {
                flex: 1;
                font-weight: 700;
                font-size: 0.9rem;
                color: #f5f5f5;
            }
            
            .smart-promo-close {
                background: rgba(255, 255, 255, 0.04);
                border: 1px solid rgba(255, 255, 255, 0.06);
                color: #a0a0a0;
                width: 28px;
                height: 28px;
                border-radius: 50%;
                cursor: pointer;
                font-size: 1.1rem;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s;
            }
            .smart-promo-close:hover {
                background: rgba(212, 175, 55, 0.08);
                color: #d4af37;
                transform: scale(1.05);
            }
            
            .smart-promo-body {
                padding: 16px 18px;
            }
            
            .smart-promo-body p {
                font-size: 0.85rem;
                color: #c0c0c0;
                line-height: 1.6;
                margin-bottom: 16px;
            }
            
            .smart-promo-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                background: linear-gradient(135deg, #d4af37, #b8962e);
                color: #0b0b0b;
                padding: 10px 20px;
                border-radius: 40px;
                text-decoration: none;
                font-weight: 700;
                font-size: 0.85rem;
                transition: all 0.3s ease;
                width: 100%;
            }
            
            .smart-promo-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(212, 175, 55, 0.25);
            }
            
            .smart-promo-footer {
                background: rgba(255, 255, 255, 0.02);
                padding: 8px 16px;
                font-size: 0.65rem;
                color: rgba(255, 255, 255, 0.2);
                text-align: center;
                border-top: 1px solid rgba(212, 175, 55, 0.04);
                letter-spacing: 0.3px;
            }
            
            /* ==========================================
               TOAST NOTIFICATION - DARK GOLD
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
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
                animation: slideIn 0.4s ease;
                display: flex;
                align-items: center;
                gap: 10px;
                border: 1px solid rgba(255, 255, 255, 0.06);
            }
            
            @keyframes slideIn {
                from { transform: translateX(100%) scale(0.9); opacity: 0; }
                to { transform: translateX(0) scale(1); opacity: 1; }
            }
            
            /* ==========================================
               RESPONSIVE
               ========================================== */
            @media (max-width: 768px) {
                .main-footer {
                    padding: 1rem 1.2rem;
                }
                
                .footer-content {
                    flex-direction: column;
                    text-align: center;
                    gap: 0.4rem;
                }
                
                .footer-left {
                    justify-content: center;
                }
                
                .footer-right {
                    justify-content: center;
                }
                
                .smart-promo-card {
                    left: 16px;
                    right: 16px;
                    width: auto;
                    bottom: 16px;
                }
                
                .toast-notification {
                    right: 16px;
                    left: 16px;
                    bottom: 16px;
                    font-size: 0.75rem;
                    padding: 10px 16px;
                    justify-content: center;
                }
            }
            
            @media (max-width: 480px) {
                .footer-left p {
                    font-size: 0.65rem;
                }
                
                .footer-credit {
                    font-size: 0.6rem;
                }
                
                .smart-promo-card {
                    border-radius: 16px;
                }
                
                .smart-promo-body p {
                    font-size: 0.75rem;
                }
            }
        `;
        document.head.appendChild(style);
    }
</script>
</body>
</html>