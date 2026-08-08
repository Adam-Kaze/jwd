<?php
$page_title = 'Kelola Kategori';
require_once '../config.php';

/** @var mysqli $conn */
global $conn;

require_login();

// --- PROSES FORM (TAMBAH / EDIT) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_action'])) {
    $cat_name = clean_input($_POST['cat_name']);
    
    if (empty($cat_name)) {
        $_SESSION['error'] = "Nama kategori wajib diisi!";
    } else {
        if ($_POST['category_action'] === 'add') {
            $stmt_check = mysqli_prepare($conn, "SELECT id FROM categories WHERE name = ?");
            mysqli_stmt_bind_param($stmt_check, "s", $cat_name);
            mysqli_stmt_execute($stmt_check);
            $result_check = mysqli_stmt_get_result($stmt_check);
            
            if (mysqli_num_rows($result_check) > 0) {
                $_SESSION['error'] = "Kategori '$cat_name' sudah ada!";
            } else {
                $stmt_insert = mysqli_prepare($conn, "INSERT INTO categories (name) VALUES (?)");
                mysqli_stmt_bind_param($stmt_insert, "s", $cat_name);
                mysqli_stmt_execute($stmt_insert);
                mysqli_stmt_close($stmt_insert);
                $_SESSION['success'] = "Kategori berhasil ditambahkan!";
            }
            mysqli_stmt_close($stmt_check);
            
        } elseif ($_POST['category_action'] === 'edit') {
            $cat_id = (int)$_POST['cat_id'];
            
            $stmt_check = mysqli_prepare($conn, "SELECT id FROM categories WHERE name = ? AND id != ?");
            mysqli_stmt_bind_param($stmt_check, "si", $cat_name, $cat_id);
            mysqli_stmt_execute($stmt_check);
            $result_check = mysqli_stmt_get_result($stmt_check);
            
            if (mysqli_num_rows($result_check) > 0) {
                $_SESSION['error'] = "Kategori '$cat_name' sudah ada!";
            } else {
                $stmt_update = mysqli_prepare($conn, "UPDATE categories SET name = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt_update, "si", $cat_name, $cat_id);
                mysqli_stmt_execute($stmt_update);
                mysqli_stmt_close($stmt_update);
                $_SESSION['success'] = "Kategori berhasil diupdate!";
            }
            mysqli_stmt_close($stmt_check);
        }
    }
    header('Location: categories.php');
    exit;
}

// --- HAPUS KATEGORI ---
if (isset($_GET['delete'])) {
    $cat_id = (int)$_GET['delete'];
    
    $stmt_check_prod = mysqli_prepare($conn, "SELECT id FROM products WHERE category_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt_check_prod, "i", $cat_id);
    mysqli_stmt_execute($stmt_check_prod);
    $result_check_prod = mysqli_stmt_get_result($stmt_check_prod);
    
    if (mysqli_num_rows($result_check_prod) > 0) {
        $_SESSION['error'] = "Kategori tidak bisa dihapus karena masih memiliki produk!";
        mysqli_stmt_close($stmt_check_prod);
    } else {
        mysqli_stmt_close($stmt_check_prod);
        $stmt_delete = mysqli_prepare($conn, "DELETE FROM categories WHERE id = ?");
        mysqli_stmt_bind_param($stmt_delete, "i", $cat_id);
        mysqli_stmt_execute($stmt_delete);
        mysqli_stmt_close($stmt_delete);
        $_SESSION['success'] = "Kategori berhasil dihapus!";
    }
    header('Location: categories.php');
    exit;
}

// --- AMBIL DATA KATEGORI UNTUK EDIT ---
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    
    $stmt_edit = mysqli_prepare($conn, "SELECT * FROM categories WHERE id = ?");
    mysqli_stmt_bind_param($stmt_edit, "i", $edit_id);
    mysqli_stmt_execute($stmt_edit);
    $result_edit = mysqli_stmt_get_result($stmt_edit);
    $edit_category = mysqli_fetch_assoc($result_edit);
    mysqli_stmt_close($stmt_edit);
    
    if (!$edit_category) {
        $_SESSION['error'] = "Kategori tidak ditemukan!";
        header('Location: categories.php');
        exit;
    }
}

// --- AMBIL SEMUA KATEGORI ---
$stmt_cat = mysqli_prepare($conn, "SELECT c.*, COUNT(p.id) as product_count 
                                    FROM categories c 
                                    LEFT JOIN products p ON c.id = p.category_id 
                                    GROUP BY c.id 
                                    ORDER BY c.id DESC");
mysqli_stmt_execute($stmt_cat);
$categories = mysqli_stmt_get_result($stmt_cat);

$success_msg = $_SESSION['success'] ?? null;
$error_msg = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

include 'includes/header.php';
?>

<div class="admin-content">

    <!-- ===== HEADER ACTIONS ===== -->
    <div class="header-actions">
        <div class="header-left">
            <h2 class="page-title-admin">
                <i class="fas fa-tags" style="color: #d4af37;"></i> 
                Kelola Kategori
            </h2>
        </div>
        <div class="header-right">
            <?php if (!$edit_category): ?>
                <a href="?add" class="btn-gold"><i class="fas fa-plus"></i> Tambah Kategori</a>
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

    <!-- ===== FORM TAMBAH/EDIT ===== -->
    <?php if (isset($_GET['add']) || $edit_category): ?>
    <div class="form-card form-card-small">
        <div class="form-header">
            <h3><i class="fas <?= $edit_category ? 'fa-edit' : 'fa-plus-circle' ?>" style="color: #d4af37;"></i> 
                <?= $edit_category ? 'Edit Kategori' : 'Tambah Kategori Baru' ?>
            </h3>
            <span class="form-badge"><?= $edit_category ? '✏️ Edit Mode' : '➕ Add Mode' ?></span>
        </div>
        <form method="POST">
            <input type="hidden" name="category_action" value="<?= $edit_category ? 'edit' : 'add' ?>">
            <?php if ($edit_category): ?>
                <input type="hidden" name="cat_id" value="<?= $edit_category['id'] ?>">
            <?php endif; ?>
            <div class="form-group">
                <label>Nama Kategori <span class="required">*</span></label>
                <input type="text" name="cat_name" value="<?= htmlspecialchars($edit_category['name'] ?? '') ?>" placeholder="Masukkan nama kategori" required autofocus>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-gold"><i class="fas fa-save"></i> Simpan</button>
                <a href="categories.php" class="btn-secondary-dark"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- ===== TABLE KATEGORI ===== -->
    <div class="table-container">
        <div class="table-header">
            <span class="table-title"><i class="fas fa-list"></i> Daftar Kategori</span>
            <span class="table-count">Total: <?= mysqli_num_rows($categories) ?> kategori</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Kategori</th>
                    <th>Jumlah Produk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($categories) > 0): ?>
                    <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                    <tr>
                        <td><span class="id-badge">#<?= $cat['id'] ?></span></td>
                        <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                        <td>
                            <span class="count-badge">
                                <i class="fas fa-box" style="font-size:0.6rem;margin-right:4px;"></i>
                                <?= $cat['product_count'] ?> produk
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="?edit=<?= $cat['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i></a>
                                <?php if ($cat['product_count'] == 0): ?>
                                    <a href="?delete=<?= $cat['id'] ?>" class="btn-delete" onclick="return confirm('Yakin hapus kategori ini?')"><i class="fas fa-trash"></i></a>
                                <?php else: ?>
                                    <span class="btn-disabled" title="Kategori memiliki produk, tidak bisa dihapus"><i class="fas fa-trash"></i></span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty-state">
                            <i class="fas fa-tags"></i>
                            <p>Belum ada kategori. Silakan tambah kategori terlebih dahulu.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
mysqli_stmt_close($stmt_cat);
include 'includes/footer.php'; 
?>