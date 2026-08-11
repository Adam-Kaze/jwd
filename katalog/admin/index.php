<?php
$page_title = 'Dashboard';
require_once '../config.php';

/** @var mysqli $conn */
global $conn;

// --- CEK LOGIN MENGGUNAKAN FUNGSI DARI CONFIG.PHP ---
require_login();

// --- 1. AMBIL DATA STATISTIK (Prepared Statement) ---
// Total Produk
$stmt_total = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM products");
mysqli_stmt_execute($stmt_total);
$res_total = mysqli_stmt_get_result($stmt_total);
$total_products = mysqli_fetch_assoc($res_total)['total'];
mysqli_stmt_close($stmt_total);

// Total Kategori
$stmt_cat = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM categories");
mysqli_stmt_execute($stmt_cat);
$res_cat = mysqli_stmt_get_result($stmt_cat);
$total_categories = mysqli_fetch_assoc($res_cat)['total'];
mysqli_stmt_close($stmt_cat);

// --- 2. AMBIL 5 PRODUK TERBARU (Prepared Statement) ---
// PASTIKAN QUERY INI MENGAMBIL 5 PRODUK TERBARU
$stmt_recent = mysqli_prepare($conn, "SELECT p.*, c.name as category_name 
                                      FROM products p 
                                      JOIN categories c ON p.category_id = c.id 
                                      ORDER BY p.id DESC LIMIT 5");
mysqli_stmt_execute($stmt_recent);
$recent_products = mysqli_stmt_get_result($stmt_recent);

// CEK JUMLAH DATA YANG DIAMBIL
$recent_count = mysqli_num_rows($recent_products);
// Reset pointer untuk looping
if ($recent_count > 0) {
    mysqli_data_seek($recent_products, 0);
}

// --- 3. AMBIL NOMOR WHATSAPP DEFAULT (Prepared Statement) ---
$wa_default = '6281383796300';
$stmt_wa = mysqli_prepare($conn, "SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1");
$key_wa = 'wa_number';
mysqli_stmt_bind_param($stmt_wa, "s", $key_wa);
mysqli_stmt_execute($stmt_wa);
$res_wa = mysqli_stmt_get_result($stmt_wa);
if ($res_wa && mysqli_num_rows($res_wa) > 0) {
    $wa_default = mysqli_fetch_assoc($res_wa)['setting_value'];
}
mysqli_stmt_close($stmt_wa);

// --- 4. DATA UNTUK CHART (Prepared Statement) ---
// AMBIL DATA KATEGORI DAN JUMLAH PRODUK
$chart_categories = [];
$chart_counts = [];
$stmt_chart = mysqli_prepare($conn, "SELECT c.name, COUNT(p.id) as total 
                                      FROM categories c 
                                      LEFT JOIN products p ON c.id = p.category_id 
                                      GROUP BY c.id 
                                      ORDER BY total DESC");
mysqli_stmt_execute($stmt_chart);
$res_chart = mysqli_stmt_get_result($stmt_chart);

// DEBUG: Cek apakah ada data
$chart_has_data = false;
while ($row = mysqli_fetch_assoc($res_chart)) {
    $chart_categories[] = $row['name'];
    $chart_counts[] = (int)$row['total'];
    $chart_has_data = true;
}
mysqli_stmt_close($stmt_chart);

// Jika tidak ada data, tambahkan data dummy untuk menampilkan chart
if (!$chart_has_data) {
    $chart_categories = ['Belum Ada Kategori'];
    $chart_counts = [1];
}

// --- 5. AMBIL SEMUA KATEGORI UNTUK STATISTIK LAIN ---
$stmt_all_categories = mysqli_prepare($conn, "SELECT c.id, c.name, COUNT(p.id) as product_count 
                                               FROM categories c 
                                               LEFT JOIN products p ON c.id = p.category_id 
                                               GROUP BY c.id 
                                               ORDER BY c.name");
mysqli_stmt_execute($stmt_all_categories);
$all_categories = mysqli_stmt_get_result($stmt_all_categories);

include 'includes/header.php';
?>

<!-- ==========================================
   DASHBOARD CONTENT — Dark Gold Theme
   ========================================== -->
<div class="dashboard-container">

    <!-- ===== STATS ROW ===== -->
    <div class="stats-row">
        <div class="stat-card gold">
            <div class="stat-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Total Produk</span>
                <span class="stat-number"><?= $total_products ?></span>
            </div>
        </div>
        <div class="stat-card gold">
            <div class="stat-icon">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Total Kategori</span>
                <span class="stat-number"><?= $total_categories ?></span>
            </div>
        </div>
        <div class="stat-card gold">
            <div class="stat-icon">
                <i class="fab fa-whatsapp" style="color: #25D366;"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">WhatsApp Default</span>
                <span class="stat-number" style="font-size:1.2rem;font-weight:600;"><?= htmlspecialchars($wa_default) ?></span>
            </div>
        </div>
    </div>

    <!-- ===== DASHBOARD GRID ===== -->
    <div class="dashboard-grid">

        <!-- Chart Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <i class="fas fa-chart-pie"></i>
                <h3>Statistik Produk per Kategori</h3>
            </div>
            <div class="chart-container">
                <canvas id="categoryChart"></canvas>
            </div>
            <!-- Tampilkan detail statistik di bawah chart -->
            <div class="chart-stats-detail">
                <?php 
                // Reset pointer untuk menampilkan detail
                mysqli_data_seek($all_categories, 0);
                $has_products = false;
                while ($cat = mysqli_fetch_assoc($all_categories)): 
                    if ($cat['product_count'] > 0) {
                        $has_products = true;
                    }
                ?>
                    <div class="stat-detail-item">
                        <span class="stat-detail-label"><?= htmlspecialchars($cat['name']) ?></span>
                        <span class="stat-detail-value"><?= $cat['product_count'] ?> produk</span>
                    </div>
                <?php endwhile; ?>
                <?php if (!$has_products && $total_products == 0): ?>
                    <div class="stat-detail-empty">
                        <i class="fas fa-info-circle"></i> Belum ada produk dalam kategori
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Products Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <i class="fas fa-clock"></i>
                <h3>Produk Terbaru</h3>
                <?php if ($recent_count > 0): ?>
                    <span class="recent-count"><?= $recent_count ?> produk</span>
                <?php endif; ?>
            </div>
            <div class="table-wrapper">
                <table class="recent-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recent_count > 0): ?>
                            <?php 
                            // Reset pointer ke awal
                            mysqli_data_seek($recent_products, 0);
                            $display_count = 0;
                            while($p = mysqli_fetch_assoc($recent_products)): 
                                $display_count++;
                            ?>
                            <tr>
                                <td><span class="id-badge">#<?= $p['id'] ?></span></td>
                                <td><?= htmlspecialchars(substr($p['name'], 0, 25)) ?><?= strlen($p['name']) > 25 ? '…' : '' ?></td>
                                <td><span class="category-badge"><?= htmlspecialchars($p['category_name']) ?></span></td>
                                <td><span class="price-tag"><?= htmlspecialchars($p['price'] ?: 'Rp 0') ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if ($display_count < 5): ?>
                                <!-- Tampilkan baris kosong jika kurang dari 5 -->
                                <?php for ($i = $display_count; $i < 5; $i++): ?>
                                <tr class="empty-row">
                                    <td colspan="4" style="color: rgba(255,255,255,0.1); text-align: center;">-</td>
                                </tr>
                                <?php endfor; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="empty-state">Belum ada produk</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== MENU NAVIGASI ===== -->
    <div class="menu-grid">
        <a href="products.php" class="menu-item">
            <div class="menu-icon"><i class="fas fa-boxes"></i></div>
            <span>Kelola Produk</span>
            <small>CRUD produk</small>
        </a>
        <a href="categories.php" class="menu-item">
            <div class="menu-icon"><i class="fas fa-list"></i></div>
            <span>Kelola Kategori</span>
            <small>CRUD kategori</small>
        </a>
        <a href="footer_settings.php" class="menu-item">
            <div class="menu-icon"><i class="fas fa-edit"></i></div>
            <span>Pengaturan Footer</span>
            <small>Edit footer</small>
        </a>
        <a href="settings.php" class="menu-item">
            <div class="menu-icon"><i class="fas fa-sliders-h"></i></div>
            <span>Pengaturan WA</span>
            <small>Nomor WhatsApp</small>
        </a>
        <a href="guide_settings.php" class="menu-item">
            <div class="menu-icon"><i class="fas fa-book"></i></div>
            <span>Panduan Invoice</span>
            <small>Edit panduan</small>
        </a>
        <a href="invoice/index.php" class="menu-item">
            <div class="menu-icon"><i class="fas fa-file-invoice"></i></div>
            <span>Invoice</span>
            <small>Kelola invoice</small>
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('categoryChart').getContext('2d');
        
        // Data dari PHP
        const labels = <?= json_encode($chart_categories) ?>;
        const data = <?= json_encode($chart_counts) ?>;
        
        // Warna untuk chart
        const colors = ['#d4af37', '#f5d77b', '#b8962e', '#e8c44a', '#a07d28', '#c9a84c', '#e0c56a'];
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 2,
                    borderColor: '#1a1a2e',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '60%',
                plugins: {
                    legend: { 
                        position: 'bottom', 
                        labels: { 
                            font: { size: 12, family: 'Inter', weight: '500' },
                            boxWidth: 14,
                            padding: 16,
                            color: '#c0c0c0',
                            usePointStyle: true,
                            pointStyle: 'circle'
                        } 
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + context.parsed + ' produk (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<style>
    /* Tambahan CSS untuk statistik detail */
    .chart-stats-detail {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid rgba(212, 175, 55, 0.15);
        display: flex;
        flex-wrap: wrap;
        gap: 8px 16px;
    }
    
    .stat-detail-item {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(212, 175, 55, 0.05);
        padding: 4px 12px;
        border-radius: 20px;
        border: 1px solid rgba(212, 175, 55, 0.08);
    }
    
    .stat-detail-label {
        color: #a0a0a0;
        font-size: 0.8rem;
    }
    
    .stat-detail-value {
        color: #d4af37;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .stat-detail-empty {
        color: #888;
        font-size: 0.85rem;
        padding: 8px 0;
        width: 100%;
        text-align: center;
    }
    
    .stat-detail-empty i {
        color: #d4af37;
        margin-right: 6px;
    }
    
    .recent-count {
        background: rgba(212, 175, 55, 0.15);
        color: #d4af37;
        font-size: 0.7rem;
        padding: 2px 10px;
        border-radius: 12px;
        font-weight: 500;
        margin-left: auto;
    }
    
    .empty-row td {
        padding: 6px 12px !important;
        height: 20px;
    }
    
    .card-header {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .card-header .recent-count {
        margin-left: auto;
    }
</style>

<?php 
// Tutup resource statement yang masih terbuka
mysqli_stmt_close($stmt_recent);
mysqli_stmt_close($stmt_all_categories);
include 'includes/footer.php'; 
?>