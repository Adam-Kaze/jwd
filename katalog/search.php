<?php
include 'config.php';

/** @var mysqli $conn */ // Memberi tahu VS Code bahwa $conn adalah object MySQLi
global $conn;            // Memastikan VS Code mengenali variabel global

// Ambil keyword dari parameter GET
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

$response = ['results' => []];

if (strlen($keyword) > 0) {
    // --- PERBAIKAN KEAMANAN: Gunakan Prepared Statement ---
    $likeKeyword = "%" . $keyword . "%";
    
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.id 
              WHERE p.name LIKE ? 
              OR p.description LIKE ?
              OR c.name LIKE ?
              ORDER BY p.id DESC 
              LIMIT 10";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $likeKeyword, $likeKeyword, $likeKeyword);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // --- PERBAIKAN: Gunakan rawurlencode agar tanda baca tidak merusak URL ---
            $image_url = !empty($row['image_url']) ? $row['image_url'] : 'https://placehold.co/400x200/1f2462/white?text=' . rawurlencode(substr($row['name'], 0, 20));
            
            $response['results'][] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'image_url' => $image_url,
                'category_name' => $row['category_name']
            ];
        }
    }
    
    // --- TAMBAHAN: Tutup statement agar server tidak lemot ---
    mysqli_stmt_close($stmt);
}

// Set header JSON
header('Content-Type: application/json');
echo json_encode($response);
?>