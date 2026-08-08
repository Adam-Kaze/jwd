<?php
// ============================================================
// AJAX HANDLER UNTUK CART
// Letakkan file ini di folder: katalog/ajax/cart_ajax.php
// ============================================================

// Include file fungsi cart (menggunakan path relatif dari folder ajax ke includes)
require_once __DIR__ . '/../includes/cart_functions.php';

// Pastikan ini hanya diakses melalui AJAX
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    die('Direct access not allowed.');
}

header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
$product_price = isset($_POST['product_price']) ? trim($_POST['product_price']) : '';

$response = ['success' => false, 'count' => 0, 'total' => 0, 'already_exists' => false];

switch ($action) {
    case 'add':
        $result = addToCart($product_id, $product_name, $product_price);
        if ($result === false) {
            $response['already_exists'] = true;
            $response['success'] = false;
            $response['message'] = 'Produk sudah ada di keranjang';
        } else {
            $response['success'] = true;
            $response['message'] = 'Produk berhasil ditambahkan';
        }
        $response['count'] = getCartCount();
        $response['total'] = getCartTotal();
        break;
        
    case 'remove':
        $response['success'] = removeFromCart($product_id);
        $response['count'] = getCartCount();
        $response['total'] = getCartTotal();
        break;
        
    case 'get':
        $response['count'] = getCartCount();
        $response['total'] = getCartTotal();
        $response['items'] = getCartItems();
        $response['success'] = true;
        break;
        
    case 'check':
        $response['success'] = true;
        $response['in_cart'] = isInCart($product_id);
        break;
        
    case 'clear':
        clearCart();
        $response['count'] = 0;
        $response['total'] = 0;
        $response['success'] = true;
        break;
}

echo json_encode($response);
exit;
?>