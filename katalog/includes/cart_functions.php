<?php
// ============================================================
// SISTEM CART / KERANJANG BELANJA - LSP COACHPRO INDONESIA
// FILE FUNGSI (HARUS DI-INCLUDE, BUKAN DI-AKSES LANGSUNG)
// ============================================================

// Pastikan session dimulai hanya jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inisialisasi cart jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/**
 * Fungsi untuk menambah produk ke cart (hanya 1 kali, tidak bisa duplicate)
 */
function addToCart($product_id, $product_name, $product_price, $quantity = 1) {
    // Sanitasi input
    $product_id = (int)$product_id;
    $product_name = htmlspecialchars(trim($product_name), ENT_QUOTES, 'UTF-8');
    $product_price = htmlspecialchars(trim($product_price), ENT_QUOTES, 'UTF-8');

    // Cek apakah produk sudah ada di cart
    if (isset($_SESSION['cart'][$product_id])) {
        return false; // Sudah ada
    } else {
        // Tambahkan ke cart
        $_SESSION['cart'][$product_id] = [
            'id' => $product_id,
            'name' => $product_name,
            'price' => $product_price,
            'quantity' => 1
        ];
        return true;
    }
}

/**
 * Fungsi untuk menghapus produk dari cart
 */
function removeFromCart($product_id) {
    $product_id = (int)$product_id;
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        return true;
    }
    return false;
}

/**
 * Fungsi untuk mendapatkan jumlah item di cart
 */
function getCartCount() {
    return count($_SESSION['cart']);
}

/**
 * Fungsi untuk mendapatkan total harga
 */
function getCartTotal() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        // Bersihkan format harga
        $price = preg_replace('/[^0-9]/', '', $item['price']);
        $total += (int)$price;
    }
    return $total;
}

/**
 * Fungsi untuk mendapatkan semua item di cart
 */
function getCartItems() {
    return $_SESSION['cart'];
}

/**
 * Fungsi untuk clear cart
 */
function clearCart() {
    $_SESSION['cart'] = [];
    return true;
}

/**
 * Fungsi untuk cek apakah produk sudah di cart
 */
function isInCart($product_id) {
    $product_id = (int)$product_id;
    return isset($_SESSION['cart'][$product_id]);
}

/**
 * Fungsi untuk get nomor WhatsApp (AMAN dengan Prepared Statement)
 */
function getWaNumber() {
    global $conn;
    
    // Jika koneksi tidak ada, kembalikan default
    if (!isset($conn)) {
        return '6281383796300';
    }

    $stmt = mysqli_prepare($conn, "SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1");
    $key = 'wa_number';
    mysqli_stmt_bind_param($stmt, "s", $key);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row['setting_value'];
    }
    
    mysqli_stmt_close($stmt);
    return '6281383796300'; // Default
}

/**
 * Generate link WhatsApp untuk checkout
 */
function getWhatsAppCheckoutLink($cart_items, $total_price, $wa_number = null) {
    if (!$wa_number) {
        $wa_number = getWaNumber();
    }
    
    $message = "Halo, saya tertarik dengan produk berikut:%0A%0A";
    $no = 1;
    foreach ($cart_items as $item) {
        $message .= $no . ". " . $item['name'] . " - " . $item['price'] . "%0A";
        $no++;
    }
    $message .= "%0ATotal: Rp " . number_format($total_price, 0, ',', '.');
    $message .= "%0A%0ATerima kasih.";
    
    return "https://wa.me/{$wa_number}?text={$message}";
}

/**
 * Generate link WhatsApp untuk konsultasi
 */
function getWhatsAppConsultationLink($wa_number = null) {
    if (!$wa_number) {
        $wa_number = getWaNumber();
    }
    
    $message = "Halo%2C%20saya%20tertarik%20dengan%20program%20sertifikasi%20di%20LSP%20COACHPRO%20INDONESIA.%0A%0A" .
               "Mohon%20informasi%20lebih%20lanjut%20mengenai%20program%20yang%20tersedia.%0A%0A" .
               "Terima%20kasih.";
    
    return "https://wa.me/{$wa_number}?text={$message}";
}
?>