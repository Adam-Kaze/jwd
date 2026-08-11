<!-- Sidebar Kategori DKV ROOM -->
<?php
/** @var mysqli $conn */
global $conn;
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-palette"></i> Kategori Kreatif
    </div>
    <ul class="category-list" id="categoryList">
        <?php
        // --- KEAMANAN: Query kategori dengan Prepared Statement ---
        $query_cat = "SELECT c.id, c.name, COUNT(p.id) as total 
                      FROM categories c 
                      LEFT JOIN products p ON c.id = p.category_id 
                      GROUP BY c.id ORDER BY c.name";
        
        $stmt_cat = mysqli_prepare($conn, $query_cat);
        mysqli_stmt_execute($stmt_cat);
        $result_cat = mysqli_stmt_get_result($stmt_cat);
        
        // --- KEAMANAN: Ambil total produk dengan Prepared Statement ---
        $stmt_total = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM products");
        mysqli_stmt_execute($stmt_total);
        $total_result = mysqli_stmt_get_result($stmt_total);
        $total_products = 0;
        if ($total_result) {
            $total_row = mysqli_fetch_assoc($total_result);
            $total_products = $total_row['total'];
        }
        mysqli_stmt_close($stmt_total);
        ?>
        <li class="category-item active" data-category="Semua" onclick="filterCategory('Semua')">
            <i class="fas fa-th-large"></i>
            <span>Semua Karya</span>
            <span class="count">(<?= $total_products ?>)</span>
        </li>
        <?php while($cat = mysqli_fetch_assoc($result_cat)): ?>
            <li class="category-item" data-category="<?= htmlspecialchars($cat['name']) ?>" onclick="filterCategory('<?= htmlspecialchars($cat['name']) ?>')">
                <i class="fas fa-tag"></i>
                <span><?= htmlspecialchars($cat['name']) ?></span>
                <span class="count">(<?= $cat['total'] ?>)</span>
            </li>
        <?php endwhile; 
        mysqli_stmt_close($stmt_cat);
        ?>
    </ul>
    
    <!-- Info DKV ROOM -->
    <div style="padding: 1rem 1.5rem; border-top: 1px solid rgba(212, 175, 55, 0.06); margin-top: 1rem;">
        <div style="background: rgba(212, 175, 55, 0.06); border-radius: 12px; padding: 1rem; border-left: 3px solid #d4af37;">
            <p style="font-size: 0.75rem; color: #d4af37; font-weight: 700; margin-bottom: 0.3rem;">
                <i class="fas fa-gem" style="color: #d4af37;"></i> DKV ROOM
            </p>
            <p style="font-size: 0.65rem; color: #a0a0a0; line-height: 1.4;">
                Solusi Desain Grafis, Ilustrasi Digital, Fotografi, dan Produksi Video
            </p>
        </div>
    </div>
    
    <div class="sidebar-footer">
        <small>
            <i class="fas fa-headset" style="color: #d4af37;"></i> 
            Butuh bantuan? 
            <?php 
            $wa_clean = get_wa_number();
            $wa_msg = "Halo DKV ROOM! 👋\nSaya berminat untuk berkonsultasi mengenai studio & layanan kreatif. Terima kasih! 🙏";
            ?>
            <a href="https://wa.me/<?= $wa_clean ?>?text=<?= rawurlencode($wa_msg) ?>" target="_blank" style="color: #d4af37; font-weight: 600; text-decoration: none;">Hubungi Studio</a>
        </small>
    </div>
</aside>

<script>
// Fungsi filter kategori (untuk mobile dan desktop)
function filterCategory(categoryName) {
    // Update active state
    document.querySelectorAll('.category-item').forEach(item => {
        item.classList.remove('active');
        if (item.dataset.category === categoryName) {
            item.classList.add('active');
        }
    });
    
    // Filter produk
    const cards = document.querySelectorAll('.product-card');
    const headerTitle = document.querySelector('.products-header h2');
    
    if (categoryName === 'Semua') {
        cards.forEach(card => {
            card.style.display = 'block';
        });
        if (headerTitle) {
            headerTitle.innerHTML = '<i class="fa-regular fa-gem" style="color: #d4af37; margin-right: 10px;"></i> Semua Karya DKV ROOM';
        }
    } else {
        cards.forEach(card => {
            const cat = card.dataset.category;
            if (cat === categoryName) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
        if (headerTitle) {
            headerTitle.innerHTML = '<i class="fa-regular fa-gem" style="color: #d4af37; margin-right: 10px;"></i> ' + categoryName;
        }
    }
    
    // Tutup sidebar di mobile
    const sidebar = document.getElementById('sidebar');
    if (sidebar && window.innerWidth <= 767) {
        sidebar.classList.remove('open');
    }
}

// --- LOGIKA JAVASCRIPT SAAT HALAMAN DIMUAT ---
document.addEventListener('DOMContentLoaded', function() {
    // 1. Set active category berdasarkan URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const category = urlParams.get('category');
    if (category) {
        document.querySelectorAll('.category-item').forEach(item => {
            if (item.dataset.category === category) {
                item.click();
            }
        });
    }

    // 2. Buat overlay untuk mobile sidebar (jika belum ada)
    if (!document.getElementById('sidebarOverlay')) {
        const overlay = document.createElement('div');
        overlay.id = 'sidebarOverlay';
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }

    // 3. Logika toggle mobile sidebar
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (mobileBtn && sidebar) {
        mobileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('open');
            if (overlay) {
                overlay.classList.toggle('active');
            }
        });
    }
    
    if (overlay && sidebar) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }
});
</script>