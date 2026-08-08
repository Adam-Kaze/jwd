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
$stmt_recent = mysqli_prepare($conn, "SELECT p.*, c.name as category_name 
                                      FROM products p 
                                      JOIN categories c ON p.category_id = c.id 
                                      ORDER BY p.id DESC LIMIT 5");
mysqli_stmt_execute($stmt_recent);
$recent_products = mysqli_stmt_get_result($stmt_recent);

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
$chart_categories = [];
$chart_counts = [];
$stmt_chart = mysqli_prepare($conn, "SELECT c.name, COUNT(p.id) as total 
                                      FROM categories c 
                                      LEFT JOIN products p ON c.id = p.category_id 
                                      GROUP BY c.id 
                                      ORDER BY total DESC LIMIT 5");
mysqli_stmt_execute($stmt_chart);
$res_chart = mysqli_stmt_get_result($stmt_chart);
while ($row = mysqli_fetch_assoc($res_chart)) {
    $chart_categories[] = $row['name'];
    $chart_counts[] = $row['total'];
}
mysqli_stmt_close($stmt_chart);

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
        </div>

        <!-- Recent Products Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <i class="fas fa-clock"></i>
                <h3>Produk Terbaru</h3>
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
                        <?php if (mysqli_num_rows($recent_products) > 0): ?>
                            <?php while($p = mysqli_fetch_assoc($recent_products)): ?>
                            <tr>
                                <td><span class="id-badge">#<?= $p['id'] ?></span></td>
                                <td><?= htmlspecialchars(substr($p['name'], 0, 25)) ?>…</td>
                                <td><span class="category-badge"><?= htmlspecialchars($p['category_name']) ?></span></td>
                                <td><span class="price-tag">Rp <?= number_format($p['price'], 0, ',', '.') ?></span></td>
                            </tr>
                            <?php endwhile; ?>
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
    const ctx = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($chart_categories) ?>,
            datasets: [{
                data: <?= json_encode($chart_counts) ?>,
                backgroundColor: ['#d4af37', '#f5d77b', '#b8962e', '#e8c44a', '#a07d28'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: { 
                    position: 'bottom', 
                    labels: { 
                        font: { size: 11, family: 'Inter' },
                        boxWidth: 12,
                        padding: 14,
                        color: '#b0b0b0'
                    } 
                }
            }
        }
    });
</script>

<?php 
// Tutup resource statement yang masih terbuka (recent_products)
mysqli_stmt_close($stmt_recent);
include 'includes/footer.php'; 
?>