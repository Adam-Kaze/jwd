<?php
include '../config.php';

// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$history = isset($_SESSION['history']) ? $_SESSION['history'] : [];
$wa_default = get_wa_number();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Riwayat Konsultasi — DKV ROOM</title>
    
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

        .history-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            background: rgba(11, 11, 11, 0.5);
        }

        .history-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: #f5f5f5;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .history-header h1 i {
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
        .history-empty {
            text-align: center;
            padding: 4rem 2rem;
        }

        .history-empty .empty-icon {
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

        .history-empty h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
            color: #f5f5f5;
        }

        .history-empty p {
            color: #a0a0a0;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            max-width: 440px;
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

        /* History List */
        .history-list {
            padding: 1.5rem 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .history-card {
            background: rgba(11, 11, 11, 0.6);
            border-radius: 18px;
            border: 1px solid rgba(212, 175, 55, 0.12);
            padding: 1.25rem;
            transition: all 0.3s ease;
        }

        .history-card:hover {
            border-color: rgba(212, 175, 55, 0.3);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
        }

        .history-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 0.75rem;
            margin-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .history-inv-no {
            font-weight: 700;
            color: #d4af37;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .history-date {
            font-size: 0.8rem;
            color: #a0a0a0;
        }

        .status-badge-sent {
            background: rgba(37, 211, 102, 0.12);
            color: #25D366;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid rgba(37, 211, 102, 0.25);
        }

        .history-items {
            margin-bottom: 1rem;
        }

        .history-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            font-size: 0.9rem;
            color: #e0e0e0;
        }

        .history-item-name {
            font-weight: 500;
        }

        .history-item-price {
            font-weight: 700;
            color: #d4af37;
            font-family: 'Playfair Display', serif;
        }

        .history-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .history-total {
            font-size: 0.9rem;
            color: #a0a0a0;
        }

        .history-total span {
            font-family: 'Playfair Display', serif;
            font-weight: 800;
            color: #d4af37;
            font-size: 1.2rem;
            margin-left: 6px;
        }

        .history-btn-group {
            display: flex;
            gap: 0.5rem;
        }

        .btn-hist-view {
            padding: 6px 14px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            color: rgba(212, 175, 55, 0.85);
            border: 1px solid rgba(212, 175, 55, 0.2);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-hist-view:hover {
            background: rgba(212, 175, 55, 0.1);
            color: #d4af37;
        }

        .btn-hist-wa {
            padding: 6px 14px;
            border-radius: 10px;
            background: rgba(37, 211, 102, 0.12);
            color: #25D366;
            border: 1px solid rgba(37, 211, 102, 0.3);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-hist-wa:hover {
            background: rgba(37, 211, 102, 0.25);
            color: #28e06f;
        }

        @media (max-width: 600px) {
            .history-header, .history-list {
                padding: 1.2rem;
            }
            .history-card-footer {
                flex-direction: column;
                align-items: stretch;
            }
            .history-btn-group {
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="history-header">
            <h1><i class="fas fa-clock-rotate-left"></i> Riwayat Konsultasi Saya</h1>
            <a href="../index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Katalog</a>
        </div>
        
        <?php if (empty($history)): ?>
            <div class="history-empty">
                <div class="empty-icon">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
                <h3>Belum Ada Riwayat Konsultasi</h3>
                <p>Setiap kali Anda menekan "Konsultasi Semua via WA" pada koleksi simpanan, riwayat konsultasi dan invoice resmi akan dicatat otomatis di sini.</p>
                <a href="../index.php" class="btn-shop"><i class="fas fa-palette"></i> Lihat Katalog Karya</a>
            </div>
        <?php else: ?>
            <div class="history-list">
                <?php foreach ($history as $h): ?>
                <div class="history-card">
                    <div class="history-card-header">
                        <div class="history-inv-no">
                            <i class="fas fa-file-invoice"></i> <?= htmlspecialchars($h['invoice_number']) ?>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="status-badge-sent"><i class="fab fa-whatsapp"></i> Terkirim WA</span>
                            <span class="history-date"><i class="far fa-clock"></i> <?= date('d M Y, H:i', strtotime($h['date'])) ?> WIB</span>
                        </div>
                    </div>
                    
                    <div class="history-items">
                        <?php foreach ($h['items'] as $item): ?>
                        <div class="history-item-row">
                            <span class="history-item-name">• <?= htmlspecialchars($item['name']) ?></span>
                            <span class="history-item-price"><?= format_rupiah($item['price']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="history-card-footer">
                        <div class="history-total">
                            Total Estimasi: <span><?= format_rupiah($h['total']) ?></span>
                        </div>
                        <div class="history-btn-group">
                            <a href="../admin/invoice/view.php?link=<?= htmlspecialchars($h['unique_link']) ?>" target="_blank" class="btn-hist-view">
                                <i class="fas fa-eye"></i> Lihat Invoice
                            </a>
                            <a href="<?= htmlspecialchars($h['wa_url']) ?>" target="_blank" class="btn-hist-wa">
                                <i class="fab fa-whatsapp"></i> Chat WA Lagi
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
