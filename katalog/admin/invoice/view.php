<?php
$page_title = 'Detail Invoice';
require_once '../../config.php';

/** @var mysqli $conn */
global $conn;

// Ambil berdasarkan ID atau unique link
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$link = isset($_GET['link']) ? trim($_GET['link']) : '';

$invoice = null;
if ($id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT i.*, p.name as product_name FROM invoices i LEFT JOIN products p ON i.product_id = p.id WHERE i.id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $invoice = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} elseif (!empty($link)) {
    $stmt = mysqli_prepare($conn, "SELECT i.*, p.name as product_name FROM invoices i LEFT JOIN products p ON i.product_id = p.id WHERE i.unique_link = ?");
    mysqli_stmt_bind_param($stmt, "s", $link);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $invoice = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (!$invoice) {
    echo "<h2 style='color:#fff; text-align:center; padding:3rem;'>Invoice tidak ditemukan!</h2>";
    exit;
}

function invoice_status_badge($status) {
    $colors = [
        'draft' => ['bg' => 'rgba(255,255,255,0.05)', 'color' => '#8ba0ae'],
        'sent' => ['bg' => 'rgba(212,175,55,0.15)', 'color' => '#d4af37'],
        'paid' => ['bg' => 'rgba(16,185,129,0.2)', 'color' => '#10b981'],
        'overdue' => ['bg' => 'rgba(239,68,68,0.2)', 'color' => '#ef4444']
    ];
    $color = $colors[$status] ?? ['bg' => 'rgba(255,255,255,0.05)', 'color' => '#8ba0ae'];
    return '<span style="background:' . $color['bg'] . ';color:' . $color['color'] . ';padding:4px 12px;border-radius:20px;font-weight:700;font-size:0.8rem;text-transform:uppercase;">' . ucfirst($status) . '</span>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= htmlspecialchars($invoice['invoice_number']) ?> — DKV ROOM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0b0b0b;
            color: #f5f5f5;
            padding: 2rem 1rem;
            min-height: 100vh;
        }

        .invoice-card {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(20, 20, 20, 0.9);
            backdrop-filter: blur(16px);
            border-radius: 24px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7);
            padding: 2.5rem;
            position: relative;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid rgba(212, 175, 55, 0.15);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: #d4af37;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-subtitle {
            color: #a0a0a0;
            font-size: 0.85rem;
            margin-top: 4px;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-number {
            font-size: 1.3rem;
            font-weight: 800;
            color: #f5f5f5;
            margin-bottom: 4px;
        }

        .invoice-dates {
            font-size: 0.85rem;
            color: #a0a0a0;
            line-height: 1.5;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
            background: rgba(11, 11, 11, 0.5);
            padding: 1.25rem 1.5rem;
            border-radius: 16px;
            border: 1px solid rgba(212, 175, 55, 0.08);
        }

        .info-block h4 {
            color: #d4af37;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .info-block p {
            color: #e0e0e0;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        .item-table th {
            text-align: left;
            padding: 12px 16px;
            background: rgba(212, 175, 55, 0.08);
            color: #d4af37;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        }

        .item-table td {
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 0.95rem;
        }

        .summary-box {
            max-width: 320px;
            margin-left: auto;
            background: rgba(11, 11, 11, 0.5);
            border-radius: 16px;
            padding: 1.25rem;
            border: 1px solid rgba(212, 175, 55, 0.1);
            margin-bottom: 2rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 0.9rem;
            color: #a0a0a0;
        }

        .summary-row.total {
            border-top: 1px solid rgba(212, 175, 55, 0.2);
            padding-top: 12px;
            margin-top: 6px;
            font-size: 1.1rem;
            font-weight: 800;
            color: #d4af37;
        }

        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(212, 175, 55, 0.15);
            padding-top: 1.5rem;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-print {
            background: linear-gradient(135deg, #d4af37, #b8962e);
            color: #0b0b0b;
            border: none;
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.35);
        }

        .btn-back-link {
            background: rgba(255, 255, 255, 0.05);
            color: #d4af37;
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .btn-back-link:hover {
            background: rgba(212, 175, 55, 0.1);
        }

        @media print {
            body { background: #fff; color: #000; padding: 0; }
            .invoice-card { background: #fff; color: #000; border: none; box-shadow: none; padding: 0; }
            .brand-title, .invoice-number, .summary-row.total, .item-table th { color: #000 !important; }
            .actions-bar { display: none !important; }
        }
    </style>
</head>
<body>

<div class="invoice-card">
    <div class="invoice-header">
        <div>
            <div class="brand-title">
                <i class="fas fa-gem"></i> DKV ROOM
            </div>
            <div class="brand-subtitle">Studio Kreatif · Desain · Ilustrasi · Foto · Video</div>
        </div>
        <div class="invoice-meta">
            <div class="invoice-number"><?= htmlspecialchars($invoice['invoice_number']) ?></div>
            <div style="margin-bottom: 8px;"><?= invoice_status_badge($invoice['status']) ?></div>
            <div class="invoice-dates">
                <div>Tanggal: <?= date('d F Y', strtotime($invoice['issue_date'])) ?></div>
                <div>Jatuh Tempo: <?= date('d F Y', strtotime($invoice['due_date'])) ?></div>
            </div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-block">
            <h4>Ditagihkan Kepada:</h4>
            <p><strong><?= htmlspecialchars($invoice['client_name']) ?></strong></p>
            <?php if (!empty($invoice['client_email'])): ?>
                <p><i class="fas fa-envelope" style="color:#d4af37; font-size:0.8rem;"></i> <?= htmlspecialchars($invoice['client_email']) ?></p>
            <?php endif; ?>
            <?php if (!empty($invoice['client_phone'])): ?>
                <p><i class="fas fa-phone" style="color:#d4af37; font-size:0.8rem;"></i> <?= htmlspecialchars(format_phone_number($invoice['client_phone'])) ?></p>
            <?php endif; ?>
            <?php if (!empty($invoice['client_address'])): ?>
                <p><i class="fas fa-map-marker-alt" style="color:#d4af37; font-size:0.8rem;"></i> <?= nl2br(htmlspecialchars($invoice['client_address'])) ?></p>
            <?php endif; ?>
        </div>

        <div class="info-block">
            <h4>Diterbitkan Oleh:</h4>
            <p><strong>DKV ROOM Studio</strong></p>
            <p><i class="fas fa-globe" style="color:#d4af37; font-size:0.8rem;"></i> dkvroom.com</p>
            <p><i class="fab fa-whatsapp" style="color:#d4af37; font-size:0.8rem;"></i> <?= format_phone_number(get_wa_number()) ?></p>
            <?php if (!empty($invoice['schedule'])): ?>
                <p style="margin-top:8px;"><strong>Jadwal:</strong> <?= htmlspecialchars($invoice['schedule']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <table class="item-table">
        <thead>
            <tr>
                <th>Deskripsi Layanan / Proyek</th>
                <th style="text-align: right;">Biaya</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($invoice['service_name']) ?></strong>
                    <?php if (!empty($invoice['service_description'])): ?>
                        <div style="color: #a0a0a0; font-size: 0.85rem; margin-top: 4px;">
                            <?= nl2br(htmlspecialchars($invoice['service_description'])) ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td style="text-align: right; font-weight: 700; color: #d4af37;">
                    <?= format_rupiah($invoice['amount']) ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-row">
            <span>Subtotal:</span>
            <span><?= format_rupiah($invoice['amount']) ?></span>
        </div>
        <?php if ($invoice['tax'] > 0): ?>
        <div class="summary-row">
            <span>Pajak:</span>
            <span>+ <?= format_rupiah($invoice['tax']) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($invoice['discount'] > 0): ?>
        <div class="summary-row">
            <span>Diskon:</span>
            <span>- <?= format_rupiah($invoice['discount']) ?></span>
        </div>
        <?php endif; ?>
        <div class="summary-row total">
            <span>Total Akhir:</span>
            <span><?= format_rupiah($invoice['total']) ?></span>
        </div>
    </div>

    <?php if (!empty($invoice['guide_content'])): ?>
    <div style="background: rgba(212, 175, 55, 0.05); border-left: 3px solid #d4af37; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; font-size: 0.9rem;">
        <strong style="color: #d4af37;"><i class="fas fa-info-circle"></i> Catatan / Panduan:</strong>
        <div style="color: #d0d0d0; margin-top: 4px;"><?= nl2br(htmlspecialchars($invoice['guide_content'])) ?></div>
    </div>
    <?php endif; ?>

    <div class="actions-bar">
        <a href="index.php" class="btn-action btn-back-link"><i class="fas fa-arrow-left"></i> Kembali</a>
        <button onclick="window.print()" class="btn-action btn-print"><i class="fas fa-print"></i> Cetak / Simpan PDF</button>
    </div>
</div>

</body>
</html>
