<?php
$page_title = 'Panduan per Produk';
require_once '../config.php';

/** @var mysqli $conn */
global $conn;

require_login();

// --- 1. BUAT TABEL PRODUCT GUIDES JIKA BELUM ADA ---
$create_table = "CREATE TABLE IF NOT EXISTS product_guides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL DEFAULT 'Panduan Penggunaan',
    content LONGTEXT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";
mysqli_query($conn, $create_table);

$error = '';
$success = '';

// --- 2. FUNGSI HELPER UNTUK PRODUK ---
function get_all_products_for_guide() {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT id, name, price FROM products ORDER BY name ASC");
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $products = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $products[$row['id']] = $row;
    }
    mysqli_stmt_close($stmt);
    return $products;
}

function get_guide_by_product($product_id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT * FROM product_guides WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $guide = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    return $guide;
}

function save_product_guide($product_id, $title, $content, $is_active) {
    global $conn;
    
    // Cek apakah sudah ada panduan untuk produk ini
    $check = get_guide_by_product($product_id);
    
    if ($check) {
        // Update
        $stmt = mysqli_prepare($conn, "UPDATE product_guides SET title = ?, content = ?, is_active = ? WHERE product_id = ?");
        mysqli_stmt_bind_param($stmt, "ssii", $title, $content, $is_active, $product_id);
    } else {
        // Insert
        $stmt = mysqli_prepare($conn, "INSERT INTO product_guides (product_id, title, content, is_active) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issi", $product_id, $title, $content, $is_active);
    }
    
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

// --- 3. AMBIL DAFTAR PRODUK ---
$products = get_all_products_for_guide();

// --- 4. AMBIL PANDUAN YANG DIPILIH ---
$selected_product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$selected_guide = null;
if ($selected_product_id > 0) {
    $selected_guide = get_guide_by_product($selected_product_id);
}

// --- 5. PROSES SIMPAN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $title = clean_input($_POST['title'] ?? 'Panduan Penggunaan');
    $content = $_POST['content'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($content)) {
        $error = 'Konten panduan tidak boleh kosong!';
    } else {
        if (save_product_guide($product_id, $title, $content, $is_active)) {
            $success = 'Panduan produk berhasil disimpan!';
            $selected_guide = get_guide_by_product($product_id);
        } else {
            $error = 'Gagal menyimpan: ' . mysqli_error($conn);
        }
    }
}

include 'includes/header.php';
?>

<div class="dashboard-card">
    <h3><i class="fas fa-box"></i> Panduan per Produk</h3>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1rem;">
        <i class="fas fa-info-circle"></i> 
        Atur panduan khusus untuk setiap produk. Jika tidak ada panduan khusus, akan menggunakan panduan default.
    </p>
    
    <!-- Pilih Produk -->
    <div class="form-group">
        <label>Pilih Produk</label>
        <select id="productSelect" onchange="window.location.href='product_guide.php?product_id='+this.value" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px;">
            <option value="0">-- Pilih Produk --</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= $p['id'] ?>" <?= ($selected_product_id == $p['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['name']) ?> - <?= format_rupiah($p['price']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <?php if ($selected_product_id > 0): ?>
        <hr>
        <form method="POST">
            <input type="hidden" name="product_id" value="<?= $selected_product_id ?>">
            
            <div class="form-group">
                <label>Judul Panduan</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($selected_guide['title'] ?? 'Panduan Penggunaan ' . htmlspecialchars($products[$selected_product_id]['name'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label>Konten Panduan (HTML diperbolehkan)</label>
                <textarea name="content" class="form-control" rows="12" style="font-family: monospace; font-size: 0.85rem;"><?= htmlspecialchars($selected_guide['content'] ?? '') ?></textarea>
            </div>
            <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="is_active" id="is_active" <?= ($selected_guide['is_active'] ?? 1) ? 'checked' : '' ?> style="width: auto;">
                <label for="is_active" style="margin: 0;">Aktifkan Panduan</label>
            </div>
            <button type="submit"><i class="fas fa-save"></i> Simpan Panduan</button>
        </form>
        
        <hr style="margin: 1.5rem 0;">
        <h4 style="font-size: 1rem;">Preview Panduan:</h4>
        <div style="border: 1px solid #e2e8f0; padding: 1rem; border-radius: 12px; background: #f8fafc; max-height: 400px; overflow-y: auto;">
            <?php if ($selected_guide && $selected_guide['is_active']): ?>
                <?= $selected_guide['content'] ?>
            <?php else: ?>
                <p style="color: #94a3b8;">Panduan belum diaktifkan atau belum dibuat.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div style="padding: 2rem; text-align: center; color: #94a3b8;">
            <i class="fas fa-hand-point-left" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
            Pilih produk di atas untuk mengatur panduannya.
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>