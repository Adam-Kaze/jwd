<?php
$page_title = 'Kelola Produk';
require_once '../config.php';

/** @var mysqli $conn */
global $conn;

require_login();

// --- PROSES FORM (TAMBAH / EDIT) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_action'])) {
    $name = clean_input($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $desc = clean_input($_POST['description']);
    $price = clean_input($_POST['price']);
    $demo_link = clean_input($_POST['demo_link']);
    $wa_number = clean_input($_POST['wa_number']);
    
    $image_url = $_POST['old_image'] ?? '';
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '';
        if (file_exists(__DIR__ . '/../uploads/')) {
            $upload_dir = __DIR__ . '/../uploads/';
        } elseif (file_exists(__DIR__ . '/uploads/')) {
            $upload_dir = __DIR__ . '/uploads/';
        } else {
            $upload_dir = __DIR__ . '/../uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
        }
        chmod($upload_dir, 0777);
        
        $file = $_FILES['product_image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $file_name = time() . '_' . rand(1000, 9999) . '.' . $ext;
        $target_file = $upload_dir . $file_name;
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($file['tmp_name']);
        $max_size = 5 * 1024 * 1024;
        
        if ($file['size'] > $max_size) {
            $_SESSION['error'] = "Ukuran file terlalu besar. Maksimal 5MB.";
        } elseif (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                if (!empty($image_url) && strpos($image_url, 'uploads/') !== false) {
                    $old_file_path = __DIR__ . '/../' . $image_url;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
                $image_url = 'uploads/' . $file_name;
            } else {
                $_SESSION['error'] = "Gagal mengupload gambar. Periksa permission folder.";
            }
        } else {
            $_SESSION['error'] = "Tipe file tidak diizinkan. Gunakan JPG, PNG, GIF, atau WEBP.";
        }
    }
    
    $errors = [];
    if (empty($name)) $errors[] = "Nama produk wajib diisi";
    if ($category_id <= 0) $errors[] = "Kategori wajib dipilih";
    
    if (empty($errors)) {
        if ($_POST['product_action'] === 'add') {
            if (empty($image_url)) {
                $image_url = 'https://placehold.co/400x300/2c3e50/white?text=No+Image';
            }
            $stmt = mysqli_prepare($conn, "INSERT INTO products (name, category_id, description, price, demo_link, image_url, wa_number) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sisssss", $name, $category_id, $desc, $price, $demo_link, $image_url, $wa_number);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Produk berhasil ditambahkan!";
            } else {
                $_SESSION['error'] = "Gagal menambah: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } elseif ($_POST['product_action'] === 'edit') {
            $id = (int)$_POST['id'];
            $stmt = mysqli_prepare($conn, "UPDATE products SET name=?, category_id=?, description=?, price=?, demo_link=?, image_url=?, wa_number=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sisssssi", $name, $category_id, $desc, $price, $demo_link, $image_url, $wa_number, $id);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Produk berhasil diupdate!";
            } else {
                $_SESSION['error'] = "Gagal mengupdate: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
        header('Location: products.php');
        exit;
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        header('Location: products.php' . ($_POST['product_action'] === 'edit' ? '?edit=' . $_POST['id'] : '?add'));
        exit;
    }
}

// --- HAPUS PRODUK ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt_img = mysqli_prepare($conn, "SELECT image_url FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt_img, "i", $id);
    mysqli_stmt_execute($stmt_img);
    $res_img = mysqli_stmt_get_result($stmt_img);
    $img_data = mysqli_fetch_assoc($res_img);
    mysqli_stmt_close($stmt_img);
    
    if ($img_data && !empty($img_data['image_url']) && strpos($img_data['image_url'], 'uploads/') !== false) {
        $file_to_delete = __DIR__ . '/../' . $img_data['image_url'];
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
        }
    }
    
    $stmt_del = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt_del, "i", $id);
    mysqli_stmt_execute($stmt_del);
    mysqli_stmt_close($stmt_del);
    
    $_SESSION['success'] = "Produk berhasil dihapus!";
    header('Location: products.php');
    exit;
}

// --- AMBIL DATA PRODUK UNTUK EDIT ---
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt_edit = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt_edit, "i", $edit_id);
    mysqli_stmt_execute($stmt_edit);
    $res_edit = mysqli_stmt_get_result($stmt_edit);
    $edit_product = mysqli_fetch_assoc($res_edit);
    mysqli_stmt_close($stmt_edit);
    
    if (!$edit_product) {
        $_SESSION['error'] = "Produk tidak ditemukan!";
        header('Location: products.php');
        exit;
    }
}

// --- QUERY PRODUK DENGAN FILTER PENCARIAN ---
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$products = [];

if (!empty($search)) {
    $like_search = "%" . $search . "%";
    $stmt_search = mysqli_prepare($conn, "SELECT p.*, c.name as category_name 
                                          FROM products p 
                                          JOIN categories c ON p.category_id = c.id 
                                          WHERE p.name LIKE ? OR c.name LIKE ? 
                                          ORDER BY p.id DESC");
    mysqli_stmt_bind_param($stmt_search, "ss", $like_search, $like_search);
    mysqli_stmt_execute($stmt_search);
    $products = mysqli_stmt_get_result($stmt_search);
} else {
    $stmt_all = mysqli_prepare($conn, "SELECT p.*, c.name as category_name 
                                       FROM products p 
                                       JOIN categories c ON p.category_id = c.id 
                                       ORDER BY p.id DESC");
    mysqli_stmt_execute($stmt_all);
    $products = mysqli_stmt_get_result($stmt_all);
}

// --- AMBIL KATEGORI ---
$stmt_cat = mysqli_prepare($conn, "SELECT * FROM categories ORDER BY name");
mysqli_stmt_execute($stmt_cat);
$categories = mysqli_stmt_get_result($stmt_cat);

// --- TAMPILKAN PESAN SESSION ---
$success_msg = $_SESSION['success'] ?? null;
$error_msg = $_SESSION['error'] ?? null;
$errors = $_SESSION['errors'] ?? [];
$old_data = $_SESSION['old_data'] ?? [];
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['errors'], $_SESSION['old_data']);

include 'includes/header.php';
?>

<div class="admin-content">

    <!-- ===== HEADER ACTIONS ===== -->
    <div class="header-actions">
        <div class="header-left">
            <h2 class="page-title-admin">
                <i class="fas fa-boxes" style="color: #d4af37;"></i> 
                Kelola Produk
            </h2>
        </div>
        <div class="header-right">
            <form method="GET" class="search-box">
                <input type="text" name="search" placeholder="Cari produk..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
                <?php if (!empty($search)): ?>
                    <a href="products.php" class="btn-reset"><i class="fas fa-times"></i></a>
                <?php endif; ?>
            </form>
            <?php if (!$edit_product): ?>
                <a href="?add" class="btn-gold">
                    <i class="fas fa-plus"></i> Tambah Produk
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- ===== ALERT MESSAGES ===== -->
    <?php if ($success_msg): ?>
        <div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- ===== FORM TAMBAH/EDIT ===== -->
    <?php if (isset($_GET['add']) || $edit_product): ?>
    <div class="form-card">
        <div class="form-header">
            <h3><i class="fas <?= $edit_product ? 'fa-edit' : 'fa-plus-circle' ?>" style="color: #d4af37;"></i> 
                <?= $edit_product ? 'Edit Produk' : 'Tambah Produk Baru' ?>
            </h3>
            <span class="form-badge"><?= $edit_product ? '✏️ Edit Mode' : '➕ Add Mode' ?></span>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_action" value="<?= $edit_product ? 'edit' : 'add' ?>">
            <?php if ($edit_product): ?>
                <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">
                <input type="hidden" name="old_image" value="<?= htmlspecialchars($edit_product['image_url'] ?? '') ?>">
            <?php endif; ?>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Produk <span class="required">*</span></label>
                    <input type="text" name="name" value="<?= htmlspecialchars($old_data['name'] ?? $edit_product['name'] ?? '') ?>" placeholder="Masukkan nama produk" required>
                </div>
                <div class="form-group">
                    <label>Kategori <span class="required">*</span></label>
                    <select name="category_id" required>
                        <option value="">— Pilih Kategori —</option>
                        <?php mysqli_data_seek($categories, 0); while($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?= $cat['id'] ?>" 
                                <?= (($edit_product && $edit_product['category_id'] == $cat['id']) || ($old_data['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="text" name="price" value="<?= htmlspecialchars($old_data['price'] ?? $edit_product['price'] ?? '') ?>" placeholder="Rp 2.500.000">
                </div>
                <div class="form-group">
                    <label>Nomor WhatsApp</label>
                    <input type="text" name="wa_number" value="<?= htmlspecialchars($old_data['wa_number'] ?? $edit_product['wa_number'] ?? '') ?>" placeholder="6281234567890">
                    <small>Kosongkan untuk menggunakan nomor default</small>
                </div>
                <div class="form-group">
                    <label>Link Demo</label>
                    <input type="text" name="demo_link" value="<?= htmlspecialchars($old_data['demo_link'] ?? $edit_product['demo_link'] ?? '') ?>" placeholder="demo/nama-produk/">
                </div>
                <div class="form-group">
                    <label>Gambar Produk</label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="product_image" id="product_image" accept="image/*">
                        <label for="product_image" class="file-label">
                            <i class="fas fa-cloud-upload-alt"></i> Pilih Gambar
                        </label>
                        <span class="file-name" id="fileName">Tidak ada file dipilih</span>
                    </div>
                    <small>Format: JPG, PNG, GIF, WEBP. Maks 5MB</small>
                    <img id="preview" class="preview-image" style="display: none;">
                    
                    <?php if ($edit_product && !empty($edit_product['image_url'])): ?>
                        <div class="current-image">
                            <strong>Gambar saat ini:</strong>
                            <div class="image-preview-wrapper">
                                <?php 
                                $image_path = $edit_product['image_url'];
                                if (strpos($image_path, 'uploads/') === 0) {
                                    $full_path = __DIR__ . '/../' . $image_path;
                                    if (file_exists($full_path)) {
                                        echo '<img src="' . htmlspecialchars($image_path) . '" alt="Current Image">';
                                    } else {
                                        echo '<img src="https://placehold.co/150x150/2c3e50/white?text=File+Not+Found">';
                                    }
                                } else {
                                    echo '<img src="' . htmlspecialchars($image_path) . '" alt="Current Image" onerror="this.src=\'https://placehold.co/150x150/2c3e50/white?text=Error\'">';
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group full-width">
                    <label>Deskripsi</label>
                    <textarea name="description" rows="4" placeholder="Deskripsikan produk secara detail..."><?= htmlspecialchars($old_data['description'] ?? $edit_product['description'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-gold"><i class="fas fa-save"></i> Simpan</button>
                <a href="products.php" class="btn-secondary-dark"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- ===== TABLE PRODUK ===== -->
    <div class="table-container">
        <div class="table-header">
            <span class="table-title"><i class="fas fa-list"></i> Daftar Produk</span>
            <span class="table-count">Total: <?= mysqli_num_rows($products) ?> produk</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>WA</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($products) > 0): ?>
                    <?php $no = 1; while($p = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td><span class="id-badge">#<?= $p['id'] ?></span></td>
                        <td>
                            <?php 
                            $image_path = $p['image_url'] ?? '';
                            $image_display = false;
                            $image_src = '';
                            
                            if (strpos($image_path, 'uploads/') === 0) {
                                $possible_paths = [
                                    __DIR__ . '/../' . $image_path,
                                    __DIR__ . '/' . $image_path,
                                    $_SERVER['DOCUMENT_ROOT'] . '/' . $image_path
                                ];
                                foreach ($possible_paths as $path) {
                                    if (file_exists($path)) {
                                        $image_display = true;
                                        $image_src = $image_path;
                                        break;
                                    }
                                }
                                if (!$image_display) {
                                    $filename = basename($image_path);
                                    $upload_dir = __DIR__ . '/../uploads/';
                                    if (file_exists($upload_dir . $filename)) {
                                        $image_display = true;
                                        $image_src = 'uploads/' . $filename;
                                    }
                                }
                            } elseif (filter_var($image_path, FILTER_VALIDATE_URL)) {
                                $image_display = true;
                                $image_src = $image_path;
                            }
                            
                            if ($image_display && !empty($image_src)) {
                                echo '<img src="' . htmlspecialchars($image_src) . '" class="product-image" alt="' . htmlspecialchars($p['name']) . '" onerror="this.src=\'https://placehold.co/50x50/2c3e50/white?text=Error\'">';
                            } else {
                                echo '<img src="https://placehold.co/50x50/2c3e50/white?text=No+Img" class="product-image" alt="No Image">';
                            }
                            ?>
                        </td>
                        <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                        <td><span class="category-badge"><?= htmlspecialchars($p['category_name']) ?></span></td>
                        <td class="price-tag"><?= htmlspecialchars($p['price']) ?></td>
                        <td><?= !empty($p['wa_number']) ? htmlspecialchars($p['wa_number']) : '<span style="color:rgba(255,255,255,0.15);">-</span>' ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="?edit=<?= $p['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i></a>
                                <a href="?delete=<?= $p['id'] ?>" class="btn-delete" onclick="return confirm('Yakin hapus produk ini?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php $no++; endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p><?= empty($search) ? 'Belum ada produk. Silakan tambah produk terlebih dahulu.' : 'Tidak ada produk yang cocok dengan pencarian.' ?></p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Preview gambar
    const fileInput = document.getElementById('product_image');
    const preview = document.getElementById('preview');
    const fileName = document.getElementById('fileName');
    
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                if (fileName) fileName.textContent = file.name;
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                if (fileName) fileName.textContent = 'Tidak ada file dipilih';
                preview.style.display = 'none';
            }
        });
    }
</script>

<?php 
if (isset($stmt_cat)) mysqli_stmt_close($stmt_cat);
if (isset($stmt_all)) mysqli_stmt_close($stmt_all);
if (isset($stmt_search)) mysqli_stmt_close($stmt_search);

include 'includes/footer.php'; 
?>