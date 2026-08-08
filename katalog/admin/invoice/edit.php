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

// Ambil data invoice
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

// Ambil produk
$products = [];
$stmt_prod = mysqli_prepare($conn, "SELECT id, name, price, description FROM products ORDER BY name");
mysqli_stmt_execute($stmt_prod);
$prod_result = mysqli_stmt_get_result($stmt_prod);
while ($row = mysqli_fetch_assoc($prod_result)) {
    $products[] = $row;
}
mysqli_stmt_close($stmt_prod);

// Proses update
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... (logika update sama seperti create, hanya dengan WHERE id = ?)
}

include '../includes/header.php';
?>

<!-- Form Edit (sama seperti create, tapi data diisi dari $invoice) -->

<?php include '../includes/footer.php'; ?>