<?php
// ============================================================
// AJAX HANDLER UNTUK CART
// Letakkan file ini di folder: katalog/ajax/cart_ajax.php
// ============================================================

// Include config & file fungsi cart
require_once __DIR__ . '/../config.php';
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
        
    case 'checkout':
        $cart_items = getCartItems();
        if (empty($cart_items)) {
            $response['success'] = false;
            $response['message'] = 'Koleksi simpanan Anda kosong!';
            break;
        }
        
        $total_price = getCartTotal();
        $wa_number = get_wa_number();
        
        // Buat pesan WhatsApp
        $wa_message = "Halo DKV ROOM,\n\nSaya berminat untuk berkonsultasi mengenai koleksi karya yang telah saya simpan berikut:\n\n";
        $no = 1;
        $service_desc_lines = [];
        foreach ($cart_items as $item) {
            $wa_message .= $no . ". *" . $item['name'] . "* - " . format_rupiah($item['price']) . "\n";
            $service_desc_lines[] = $no . ". " . $item['name'] . " - " . format_rupiah($item['price']);
            $no++;
        }
        $wa_message .= "\n*Total Estimasi Biaya:* " . format_rupiah($total_price) . "\n\n";
        $wa_message .= "Mohon informasi lebih lanjut mengenai pengerjaan dan ketersediaan slot. Terima kasih!";
        
        $wa_url = "https://wa.me/{$wa_number}?text=" . rawurlencode($wa_message);
        
        // Buat Invoice otomatis di Database Admin
        global $conn;
        $invoice_number = 'INV-DKV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $unique_link = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 12);
        
        $client_name = 'Klien Simpanan Website';
        $service_name = 'Konsultasi Koleksi Karya (' . count($cart_items) . ' item)';
        $service_description = implode("\n", $service_desc_lines);
        $status = 'sent';
        $issue_date = date('Y-m-d');
        $due_date = date('Y-m-d', strtotime('+30 days'));
        
        $stmt_ins = mysqli_prepare($conn, "INSERT INTO invoices (
            invoice_number, client_name, service_name, service_description,
            amount, tax, discount, total, status, issue_date, due_date, unique_link
        ) VALUES (?, ?, ?, ?, ?, 0, 0, ?, ?, ?, ?, ?)");
        
        if ($stmt_ins) {
            mysqli_stmt_bind_param($stmt_ins, "ssssddssss",
                $invoice_number, $client_name, $service_name, $service_description,
                $total_price, $total_price, $status, $issue_date, $due_date, $unique_link
            );
            mysqli_stmt_execute($stmt_ins);
            mysqli_stmt_close($stmt_ins);
        }
        
        // Simpan ke Riwayat Konsultasi Session User
        if (!isset($_SESSION['history'])) {
            $_SESSION['history'] = [];
        }
        
        $history_item = [
            'id' => time(),
            'invoice_number' => $invoice_number,
            'unique_link' => $unique_link,
            'items' => $cart_items,
            'total' => $total_price,
            'date' => date('Y-m-d H:i:s'),
            'wa_url' => $wa_url
        ];
        array_unshift($_SESSION['history'], $history_item);
        
        // Otomatis Kosongkan Keranjang Simpanan
        clearCart();
        
        $response['success'] = true;
        $response['wa_url'] = $wa_url;
        $response['invoice_number'] = $invoice_number;
        $response['count'] = 0;
        $response['history_count'] = count($_SESSION['history']);
        break;
}

echo json_encode($response);
exit;
?>