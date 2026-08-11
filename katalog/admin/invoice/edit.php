<?php
$page_title = 'Edit Invoice';
require_once '../../config.php';

/** @var mysqli $conn */
global $conn;

require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['error'] = 'Invoice tidak ditemukan!';
    header('Location: index.php');
    exit;
}

// Ambil data invoice dari DB
$stmt = mysqli_prepare($conn, "SELECT * FROM invoices WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$invoice = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$invoice) {
    $_SESSION['error'] = 'Invoice tidak ditemukan!';
    header('Location: index.php');
    exit;
}

// Ambil list produk untuk dropdown
$products = [];
$stmt_prod = mysqli_prepare($conn, "SELECT id, name, price, description FROM products ORDER BY name");
if ($stmt_prod) {
    mysqli_stmt_execute($stmt_prod);
    $prod_result = mysqli_stmt_get_result($stmt_prod);
    while ($row = mysqli_fetch_assoc($prod_result)) {
        $products[] = $row;
    }
    mysqli_stmt_close($stmt_prod);
}

$error = '';
$success = '';
$form_data = $invoice;

// Proses Update Form (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $client_name = trim($_POST['client_name'] ?? '');
    $client_email = trim($_POST['client_email'] ?? '');
    $client_phone = trim($_POST['client_phone'] ?? '');
    $client_address = trim($_POST['client_address'] ?? '');
    $service_name = trim($_POST['service_name'] ?? '');
    $service_description = trim($_POST['service_description'] ?? '');
    $guide_content = trim($_POST['guide_content'] ?? '');
    $schedule = trim($_POST['schedule'] ?? '');
    
    $price_raw = $_POST['price'] ?? '0';
    $price_clean = preg_replace('/[^0-9]/', '', $price_raw);
    $amount = floatval($price_clean);
    
    $tax = isset($_POST['tax']) ? floatval($_POST['tax']) : 0;
    $discount = isset($_POST['discount']) ? floatval($_POST['discount']) : 0;
    $issue_date = $_POST['issue_date'] ?? date('Y-m-d');
    $due_date = $_POST['due_date'] ?? date('Y-m-d', strtotime('+30 days'));
    $notes = trim($_POST['notes'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    
    $form_data = array_merge($invoice, [
        'product_id' => $product_id,
        'client_name' => $client_name,
        'client_email' => $client_email,
        'client_phone' => $client_phone,
        'client_address' => $client_address,
        'service_name' => $service_name,
        'service_description' => $service_description,
        'guide_content' => $guide_content,
        'schedule' => $schedule,
        'amount' => $amount,
        'tax' => $tax,
        'discount' => $discount,
        'issue_date' => $issue_date,
        'due_date' => $due_date,
        'notes' => $notes,
        'status' => $status
    ]);
    
    $errors = [];
    if (empty($client_name)) $errors[] = 'Nama klien wajib diisi!';
    if (empty($service_name)) $errors[] = 'Nama layanan wajib diisi!';
    if ($amount <= 0) $errors[] = 'Jumlah wajib diisi dan harus lebih dari 0!';
    
    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    } else {
        $total = $amount + $tax - $discount;
        if ($total < 0) $total = 0;
        
        $stmt_update = mysqli_prepare($conn, "UPDATE invoices SET 
            product_id = ?, client_name = ?, client_email = ?, client_phone = ?, client_address = ?,
            service_name = ?, service_description = ?, guide_content = ?, schedule = ?,
            amount = ?, tax = ?, discount = ?, total = ?,
            status = ?, issue_date = ?, due_date = ?, notes = ?
            WHERE id = ?");
        
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update,
                "issssssssdddsssssi",
                $product_id, $client_name, $client_email, $client_phone, $client_address,
                $service_name, $service_description, $guide_content, $schedule,
                $amount, $tax, $discount, $total,
                $status, $issue_date, $due_date, $notes, $id
            );
            
            if (mysqli_stmt_execute($stmt_update)) {
                $success = 'Invoice berhasil diperbarui!';
                mysqli_stmt_close($stmt_update);
                header("refresh:1.5;url=index.php");
            } else {
                $error = 'Gagal memperbarui invoice: ' . mysqli_error($conn);
                mysqli_stmt_close($stmt_update);
            }
        } else {
            $error = 'Gagal menyiapkan query update: ' . mysqli_error($conn);
        }
    }
}

include '../includes/header.php';
?>

<style>
.admin-content {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(212, 175, 55, 0.1);
}

.page-title-admin {
    font-size: 1.5rem;
    font-weight: 700;
    color: #f5f5f5;
    font-family: 'Playfair Display', serif;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.btn-secondary-dark {
    background: rgba(255, 255, 255, 0.04);
    color: rgba(212, 175, 55, 0.85);
    padding: 0.65rem 1.5rem;
    border: 1px solid rgba(212, 175, 55, 0.2);
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
}

.btn-secondary-dark:hover {
    background: rgba(212, 175, 55, 0.1);
    color: #d4af37;
    border-color: rgba(212, 175, 55, 0.4);
    transform: translateY(-1px);
}

.form-card {
    background: rgba(20, 20, 20, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 16px 45px rgba(0,0,0,0.5);
    border: 1px solid rgba(212, 175, 55, 0.15);
    border-top: 4px solid #d4af37;
    color: #f5f5f5;
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(212, 175, 55, 0.1);
}

.form-header h3 {
    color: #d4af37;
    margin: 0;
    font-size: 1.2rem;
    font-family: 'Playfair Display', serif;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-badge {
    background: #d4af37;
    color: #0b0b0b;
    padding: 0.25rem 0.85rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.form-grid-2col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.form-grid-2col-small {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-grid-4col {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 1rem;
}

.form-section {
    background: rgba(11, 11, 11, 0.6);
    border-radius: 14px;
    padding: 1.5rem;
    border: 1px solid rgba(212, 175, 55, 0.08);
}

.form-section.full-width {
    grid-column: 1 / -1;
}

.section-title {
    font-size: 1rem;
    font-weight: 700;
    color: #d4af37;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid rgba(212, 175, 55, 0.2);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-family: 'Playfair Display', serif;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    font-size: 0.85rem;
    font-weight: 500;
    color: #d0d0d0;
    margin-bottom: 0.4rem;
}

.form-group label .required {
    color: #ef4444;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.65rem 0.85rem;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.04);
    color: #f5f5f5;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.form-group select option {
    background: #161616;
    color: #f5f5f5;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: rgba(212, 175, 55, 0.4);
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.08);
    background: rgba(255, 255, 255, 0.06);
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: rgba(255, 255, 255, 0.25);
}

.input-with-icon {
    position: relative;
}

.input-with-icon .input-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #d4af37;
    font-size: 0.85rem;
    font-weight: 700;
}

.input-with-icon input {
    padding-left: 2.5rem !important;
}

.form-actions {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(212, 175, 55, 0.1);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.btn-gold {
    background: linear-gradient(135deg, #d4af37, #b8962e);
    color: #0b0b0b;
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.25);
}

.btn-gold:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
    background: linear-gradient(135deg, #e0c040, #c4a032);
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    padding: 1rem 1.25rem;
    border-radius: 10px;
    border-left: 4px solid #10b981;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    color: #f87171;
    padding: 1rem 1.25rem;
    border-radius: 10px;
    border-left: 4px solid #ef4444;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
</style>

<div class="admin-content">

    <div class="header-actions">
        <h2 class="page-title-admin">
            <i class="fas fa-edit" style="color: #d4af37;"></i> 
            Edit Invoice: <?= htmlspecialchars($invoice['invoice_number']) ?>
        </h2>
        <a href="index.php" class="btn-secondary-dark">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div><?= $error ?></div>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            <div><?= $success ?></div>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-card">
            <div class="form-header">
                <h3><i class="fas fa-file-signature"></i> Perbarui Data Invoice</h3>
                <span class="form-badge">No. <?= htmlspecialchars($invoice['invoice_number']) ?></span>
            </div>

            <div class="form-grid-2col">
                <!-- DATA KLIEN -->
                <div class="form-section">
                    <h4 class="section-title"><i class="fas fa-user"></i> Data Klien</h4>
                    
                    <div class="form-group">
                        <label>Nama Klien <span class="required">*</span></label>
                        <input type="text" name="client_name" value="<?= htmlspecialchars($form_data['client_name'] ?? '') ?>" placeholder="Masukkan nama klien" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="client_email" value="<?= htmlspecialchars($form_data['client_email'] ?? '') ?>" placeholder="email@domain.com">
                    </div>

                    <div class="form-group">
                        <label>Telepon / WA</label>
                        <input type="text" name="client_phone" value="<?= htmlspecialchars(format_phone_number($form_data['client_phone'] ?? '')) ?>" placeholder="081234567890">
                    </div>

                    <div class="form-group">
                        <label>Alamat Klien</label>
                        <textarea name="client_address" rows="3" placeholder="Masukkan alamat lengkap"><?= htmlspecialchars($form_data['client_address'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- DATA LAYANAN -->
                <div class="form-section">
                    <h4 class="section-title"><i class="fas fa-briefcase"></i> Data Layanan &amp; Biaya</h4>

                    <div class="form-group">
                        <label>Pilih Produk Katalog (Opsional)</label>
                        <select name="product_id" id="product_id" onchange="autoFillProduct(this)">
                            <option value="0">-- Pilih Produk Katalog --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>" 
                                        data-price="<?= htmlspecialchars($p['price']) ?>" 
                                        data-desc="<?= htmlspecialchars($p['description']) ?>"
                                        <?= ($form_data['product_id'] ?? 0) == $p['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['name']) ?> (<?= format_rupiah($p['price']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Layanan / Proyek <span class="required">*</span></label>
                        <input type="text" name="service_name" id="service_name" value="<?= htmlspecialchars($form_data['service_name'] ?? '') ?>" placeholder="Masukkan nama layanan" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Layanan</label>
                        <textarea name="service_description" id="service_description" rows="2" placeholder="Deskripsikan layanan"><?= htmlspecialchars($form_data['service_description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-grid-2col-small">
                        <div class="form-group">
                            <label>Biaya / Jumlah <span class="required">*</span></label>
                            <div class="input-with-icon">
                                <span class="input-icon">Rp</span>
                                <input type="text" name="price" id="price" value="<?= htmlspecialchars(format_rupiah($form_data['amount'] ?? 0)) ?>" placeholder="0" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status Invoice</label>
                            <select name="status">
                                <option value="draft" <?= ($form_data['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="sent" <?= ($form_data['status'] ?? '') === 'sent' ? 'selected' : '' ?>>Sent</option>
                                <option value="paid" <?= ($form_data['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid (Lunas)</option>
                                <option value="overdue" <?= ($form_data['status'] ?? '') === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2col-small">
                        <div class="form-group">
                            <label>Pajak (Rp)</label>
                            <input type="number" name="tax" value="<?= htmlspecialchars($form_data['tax'] ?? 0) ?>" step="1000">
                        </div>
                        <div class="form-group">
                            <label>Diskon (Rp)</label>
                            <input type="number" name="discount" value="<?= htmlspecialchars($form_data['discount'] ?? 0) ?>" step="1000">
                        </div>
                    </div>
                </div>

                <!-- TANGGAL & CATATAN -->
                <div class="form-section full-width">
                    <h4 class="section-title"><i class="fas fa-calendar-alt"></i> Tanggal &amp; Catatan Tambahan</h4>

                    <div class="form-grid-4col">
                        <div class="form-group">
                            <label>Tanggal Terbit</label>
                            <input type="date" name="issue_date" value="<?= htmlspecialchars($form_data['issue_date'] ?? date('Y-m-d')) ?>">
                        </div>

                        <div class="form-group">
                            <label>Jatuh Tempo</label>
                            <input type="date" name="due_date" value="<?= htmlspecialchars($form_data['due_date'] ?? date('Y-m-d', strtotime('+30 days'))) ?>">
                        </div>

                        <div class="form-group" style="grid-column: span 2;">
                            <label>Jadwal Pengerjaan</label>
                            <input type="text" name="schedule" value="<?= htmlspecialchars($form_data['schedule'] ?? '') ?>" placeholder="Misal: 12 Aug - 20 Aug 2026">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Catatan Tambahan / Panduan</label>
                        <textarea name="guide_content" rows="2" placeholder="Panduan atau catatan khusus untuk klien"><?= htmlspecialchars($form_data['guide_content'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Catatan Internal (Notes)</label>
                        <input type="text" name="notes" value="<?= htmlspecialchars($form_data['notes'] ?? '') ?>" placeholder="Catatan internal admin">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn-secondary-dark">Batal</a>
                <button type="submit" class="btn-gold">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function autoFillProduct(select) {
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption && select.value > 0) {
        const name = selectedOption.text.split(' (')[0];
        const price = selectedOption.dataset.price || '';
        const desc = selectedOption.dataset.desc || '';
        
        document.getElementById('service_name').value = name;
        document.getElementById('price').value = price;
        if (desc) {
            document.getElementById('service_description').value = desc;
        }
    }
}
</script>

<?php include '../includes/footer.php'; ?>