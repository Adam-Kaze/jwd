<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = 'Daftar Invoice - DKV ROOM';
require_once '../../config.php';

/** @var mysqli $conn */
global $conn;

require_login();

function invoice_status_badge($status) {
    $colors = [
        'draft' => ['bg' => 'rgba(255,255,255,0.05)', 'color' => '#8ba0ae'],
        'sent' => ['bg' => 'rgba(212,175,55,0.08)', 'color' => '#d4af37'],
        'paid' => ['bg' => 'rgba(16,185,129,0.1)', 'color' => '#10b981'],
        'overdue' => ['bg' => 'rgba(239,68,68,0.1)', 'color' => '#ef4444']
    ];
    $color = $colors[$status] ?? ['bg' => 'rgba(255,255,255,0.05)', 'color' => '#8ba0ae'];
    
    return '<span class="status-badge" style="background:' . $color['bg'] . ';color:' . $color['color'] . ';">' . ucfirst($status) . '</span>';
}

$invoices = [];

$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'invoices'");
if (mysqli_num_rows($check_table) == 0) {
    die("Tabel 'invoices' belum ada di database. Silakan buat tabel terlebih dahulu.");
}

$stmt = mysqli_prepare($conn, "SELECT i.*, p.name as product_name 
                                FROM invoices i 
                                LEFT JOIN products p ON i.product_id = p.id 
                                ORDER BY i.id DESC");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $invoices[] = $row;
}
mysqli_stmt_close($stmt);

$total_invoice = count($invoices);
$total_amount = 0;
$paid_count = 0;
$pending_count = 0;
foreach ($invoices as $inv) {
    $total_amount += $inv['total'];
    if ($inv['status'] == 'paid') $paid_count++;
    if (in_array($inv['status'], ['draft', 'sent'])) $pending_count++;
}

include '../includes/header.php';
?>

<div class="admin-content invoice-page">

    <!-- Header Actions -->
    <div class="header-actions">
        <h2 class="page-subtitle"><i class="fas fa-file-invoice" style="color: #d4af37;"></i> Daftar Invoice</h2>
        <a href="create.php" class="btn-gold"><i class="fas fa-plus"></i> Invoice Baru</a>
    </div>

    <!-- Statistik Premium -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
            <div class="stat-info">
                <span class="stat-label">Total Invoice</span>
                <span class="stat-number"><?= $total_invoice ?></span>
            </div>
        </div>
        <div class="stat-card gold-border">
            <div class="stat-icon" style="color: #d4af37;"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-info">
                <span class="stat-label">Total Pendapatan</span>
                <span class="stat-number" style="color: #d4af37;"><?= format_rupiah($total_amount) ?></span>
            </div>
        </div>
        <div class="stat-card green-border">
            <div class="stat-icon" style="color: #10b981;"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <span class="stat-label">Lunas</span>
                <span class="stat-number" style="color: #10b981;"><?= $paid_count ?></span>
            </div>
        </div>
        <div class="stat-card orange-border">
            <div class="stat-icon" style="color: #f59e0b;"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <span class="stat-label">Pending</span>
                <span class="stat-number" style="color: #f59e0b;"><?= $pending_count ?></span>
            </div>
        </div>
    </div>

    <!-- Tabel -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. Invoice</th>
                    <th>Klien</th>
                    <th>Produk/Layanan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Jatuh Tempo</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($invoices)): ?>
                    <tr>
                        <td colspan="8" class="empty-state">
                            <i class="fas fa-file-invoice"></i>
                            <p>Belum ada invoice.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($invoices as $i => $inv): ?>
                    <tr>
                        <td><span class="id-badge"><?= $i+1 ?></span></td>
                        <td><strong class="invoice-number"><?= htmlspecialchars($inv['invoice_number']) ?></strong></td>
                        <td><?= htmlspecialchars($inv['client_name']) ?></td>
                        <td>
                            <?php if ($inv['product_id'] > 0 && !empty($inv['product_name'])): ?>
                                <span class="category-badge"><?= htmlspecialchars($inv['product_name']) ?></span>
                            <?php else: ?>
                                <span style="color: #8ba0ae;"><?= htmlspecialchars($inv['service_name']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="price-tag"><?= format_rupiah($inv['total']) ?></td>
                        <td><?= invoice_status_badge($inv['status']) ?></td>
                        <td><?= date('d/m/Y', strtotime($inv['due_date'])) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="edit.php?id=<?= $inv['id'] ?>" class="btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="view.php?id=<?= $inv['id'] ?>" target="_blank" class="btn-view" title="Lihat Invoice"><i class="fas fa-eye"></i></a>
                                <a href="view.php?id=<?= $inv['id'] ?>" target="_blank" class="btn-pdf" title="Cetak / PDF"><i class="fas fa-file-pdf"></i></a>
                                <a href="delete.php?id=<?= $inv['id'] ?>" class="btn-delete" onclick="return confirm('Yakin hapus invoice ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>