<?php
$page_title = 'Pengaturan Website';
require_once '../config.php';

/** @var mysqli $conn */
global $conn;

require_login();

// --- PROSES SIMPAN SETTING (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    
    $site_name = clean_input($_POST['site_name']);
    $wa_number = clean_phone_number(clean_input($_POST['wa_number']));
    $footer_copyright = clean_input($_POST['footer_copyright']);
    $footer_credit_left = clean_input($_POST['footer_credit_left']);
    $footer_credit_right = clean_input($_POST['footer_credit_right']);
    
    $cta_title = clean_input($_POST['cta_title']);
    $cta_message = clean_input($_POST['cta_message']);
    $cta_emoji = clean_input($_POST['cta_emoji']);
    $cta_btn_text = clean_input($_POST['cta_btn_text']);
    $cta_wa_text = clean_input($_POST['cta_wa_text']);
    $cta_footer_text = clean_input($_POST['cta_footer_text']);
    
    if (!preg_match('/^[0-9]{10,15}$/', $wa_number)) {
        $_SESSION['error'] = "Nomor WhatsApp tidak valid! Harus angka 10-15 digit.";
        header('Location: settings.php');
        exit;
    }

    $logo_path = null;
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_image($_FILES['site_logo'], '../uploads/');
        if ($upload_result['success']) {
            $logo_path = 'uploads/' . basename($upload_result['path']);
        } else {
            $_SESSION['error'] = "Gagal upload logo: " . $upload_result['message'];
            header('Location: settings.php');
            exit;
        }
    }

    $updates = [
        'site_name' => $site_name,
        'wa_number' => $wa_number,
        'footer_copyright' => $footer_copyright,
        'footer_credit_left' => $footer_credit_left,
        'footer_credit_right' => $footer_credit_right,
        'cta_title' => $cta_title,
        'cta_message' => $cta_message,
        'cta_emoji' => $cta_emoji,
        'cta_btn_text' => $cta_btn_text,
        'cta_wa_text' => $cta_wa_text,
        'cta_footer_text' => $cta_footer_text
    ];

    if ($logo_path !== null) {
        $updates['site_logo'] = $logo_path;
    }

    foreach ($updates as $key => $value) {
        $stmt_update = mysqli_prepare($conn, "UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        mysqli_stmt_bind_param($stmt_update, "ss", $value, $key);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);
    }

    $_SESSION['success'] = "Semua pengaturan website berhasil disimpan!";
    header('Location: settings.php');
    exit;
}

// --- AMBIL DATA SETTING SAAT INI ---
$current_settings = [];
$keys_to_fetch = [
    'site_logo', 'site_name', 'wa_number', 'footer_copyright',
    'footer_credit_left', 'footer_credit_right',
    'cta_title', 'cta_message', 'cta_emoji', 'cta_btn_text',
    'cta_wa_text', 'cta_footer_text'
];

foreach ($keys_to_fetch as $key) {
    $stmt_get = mysqli_prepare($conn, "SELECT setting_value FROM settings WHERE setting_key = ?");
    mysqli_stmt_bind_param($stmt_get, "s", $key);
    mysqli_stmt_execute($stmt_get);
    $res_get = mysqli_stmt_get_result($stmt_get);
    $row = mysqli_fetch_assoc($res_get);
    
    if ($row) {
        $current_settings[$key] = $row['setting_value'];
    } else {
        $defaults = [
            'site_logo' => '',
            'site_name' => 'DKV ROOM',
            'wa_number' => '6281383796300',
            'footer_copyright' => 'DKV ROOM – Desain, Ilustrasi, Foto & Video',
            'footer_credit_left' => '<i class="fas fa-mobile-alt"></i> Mobile Friendly',
            'footer_credit_right' => '<i class="fas fa-tachometer-alt"></i> Kinerja Cepat',
            'cta_title' => '💡 Konsultasi Gratis!',
            'cta_message' => 'Butuh bantuan untuk proyek desain, ilustrasi, atau produksi video? Tim kami siap membantu!',
            'cta_emoji' => '🎨',
            'cta_btn_text' => 'Hubungi Sekarang →',
            'cta_wa_text' => 'Halo, saya butuh konsultasi untuk proyek kreatif di DKV ROOM.',
            'cta_footer_text' => '⭐ 100+ klien puas | ⚡ Respon cepat | 🎯 Kualitas terbaik'
        ];
        $current_settings[$key] = $defaults[$key];
    }
    mysqli_stmt_close($stmt_get);
}

$success_msg = $_SESSION['success'] ?? null;
$error_msg = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

include 'includes/header.php';
?>

<div class="admin-content settings-page">

    <!-- ===== HEADER ===== -->
    <div class="header-actions">
        <div class="header-left">
            <h2 class="page-title-admin">
                <i class="fas fa-sliders-h" style="color: #d4af37;"></i> 
                Pengaturan Website
            </h2>
        </div>
    </div>

    <!-- ===== ALERT MESSAGES ===== -->
    <?php if ($success_msg): ?>
        <div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <!-- ===== FORM SETTINGS ===== -->
    <div class="form-card">
        <div class="form-header">
            <h3><i class="fas fa-cogs" style="color: #d4af37;"></i> Pengaturan Website &amp; Promo</h3>
        </div>
        
        <form method="POST" enctype="multipart/form-data">
            
            <!-- Logo & Branding -->
            <div class="settings-section">
                <h4><i class="fas fa-image" style="color: #d4af37;"></i> Logo &amp; Branding</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Logo Website</label>
                        <div class="logo-upload-wrapper">
                            <?php if (!empty($current_settings['site_logo'])): ?>
                                <img src="../<?= htmlspecialchars($current_settings['site_logo']) ?>" alt="Logo" class="logo-preview">
                            <?php else: ?>
                                <div class="logo-placeholder"><i class="fas fa-image"></i> Belum ada</div>
                            <?php endif; ?>
                            <div class="file-upload-wrapper">
                                <input type="file" name="site_logo" id="site_logo" accept="image/png,image/jpeg,image/webp">
                                <label for="site_logo" class="file-label">
                                    <i class="fas fa-cloud-upload-alt"></i> Ganti Logo
                                </label>
                            </div>
                        </div>
                        <small>Format: PNG, JPG, WebP. Maks 5MB</small>
                    </div>
                    <div class="form-group">
                        <label>Nama Website <span class="required">*</span></label>
                        <input type="text" name="site_name" value="<?= htmlspecialchars($current_settings['site_name']) ?>" placeholder="Nama website" required>
                    </div>
                </div>
            </div>

            <!-- WhatsApp -->
            <div class="settings-section">
                <h4><i class="fab fa-whatsapp" style="color: #25D366;"></i> Kontak WhatsApp</h4>
                <div class="form-group">
                    <label>Nomor WhatsApp Default <span class="required">*</span></label>
                    <input type="text" name="wa_number" value="<?= htmlspecialchars(format_phone_number($current_settings['wa_number'])) ?>" placeholder="+62 813-8379-6300" required>
                    <small>Mendukung input format 08xxxxxxxx, 628xxxxxxxx, atau +62 8xx-xxxx-xxxx (otomatis rapi &amp; standar).</small>
                </div>
            </div>

            <!-- Footer -->
            <div class="settings-section">
                <h4><i class="fas fa-edit" style="color: #d4af37;"></i> Konten Footer</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Copyright Footer</label>
                        <input type="text" name="footer_copyright" value="<?= htmlspecialchars($current_settings['footer_copyright']) ?>" placeholder="Copyright text">
                    </div>
                    <div class="form-group">
                        <label>Kredit Kiri (HTML)</label>
                        <input type="text" name="footer_credit_left" value="<?= htmlspecialchars($current_settings['footer_credit_left']) ?>" placeholder="<i class='fas fa...'></i> Text">
                    </div>
                    <div class="form-group">
                        <label>Kredit Kanan (HTML)</label>
                        <input type="text" name="footer_credit_right" value="<?= htmlspecialchars($current_settings['footer_credit_right']) ?>" placeholder="<i class='fas fa...'></i> Text">
                    </div>
                </div>
                <small>Anda boleh menggunakan kode HTML seperti <code>&lt;i&gt;</code> untuk icon.</small>
            </div>

            <!-- Smart Promo CTA -->
            <div class="settings-section">
                <h4><i class="fas fa-bullhorn" style="color: #d4af37;"></i> Smart Promo CTA</h4>
                <p class="section-desc">
                    <i class="fas fa-info-circle"></i> 
                    Konten ini akan muncul sebagai kartu promosi melayang di pojok kiri bawah website.
                </p>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Judul CTA</label>
                        <input type="text" name="cta_title" value="<?= htmlspecialchars($current_settings['cta_title']) ?>" placeholder="💡 Konsultasi Gratis!">
                    </div>
                    <div class="form-group">
                        <label>Emoji / Ikon</label>
                        <input type="text" name="cta_emoji" value="<?= htmlspecialchars($current_settings['cta_emoji']) ?>" placeholder="🎨">
                    </div>
                    <div class="form-group full-width">
                        <label>Pesan Utama</label>
                        <textarea name="cta_message" rows="3" placeholder="Pesan utama CTA..."><?= htmlspecialchars($current_settings['cta_message']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Teks Tombol</label>
                        <input type="text" name="cta_btn_text" value="<?= htmlspecialchars($current_settings['cta_btn_text']) ?>" placeholder="Hubungi Sekarang →">
                    </div>
                    <div class="form-group">
                        <label>Pesan WhatsApp</label>
                        <input type="text" name="cta_wa_text" value="<?= htmlspecialchars($current_settings['cta_wa_text']) ?>" placeholder="Halo, saya butuh konsultasi...">
                    </div>
                    <div class="form-group full-width">
                        <label>Footer CTA</label>
                        <input type="text" name="cta_footer_text" value="<?= htmlspecialchars($current_settings['cta_footer_text']) ?>" placeholder="⭐ 100+ klien puas | ⚡ Respon cepat">
                    </div>
                </div>
            </div>

            <button type="submit" name="save_settings" class="btn-gold btn-full">
                <i class="fas fa-save"></i> Simpan Semua Pengaturan
            </button>
            
        </form>
    </div>
    
</div>

<?php include 'includes/footer.php'; ?>