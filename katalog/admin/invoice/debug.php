<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1 style='color: blue;'>Debug Mode Aktif</h1>";
echo "<p>Halaman invoice/index.php berhasil dimuat!</p>";
echo "<p>Jika Anda melihat pesan ini, artinya:</p>";
echo "<ul>";
echo "<li>File index.php terbaca dengan benar.</li>";
echo "<li>Server tidak crash di level dasar.</li>";
echo "<li>Error 500 sebelumnya berasal dari logika database atau include path.</li>";
echo "</ul>";
echo "<hr>";
echo "<p><strong>Langkah selanjutnya:</strong> Ganti kode ini dengan versi final.</p>";
exit;
?>