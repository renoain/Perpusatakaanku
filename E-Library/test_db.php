<?php
// Simpan file ini sebagai test_db.php di root folder perpustakaan
// Akses via: http://localhost/E-library/test_db.php

require_once 'config.php';

echo "<h2>🔍 Database Connection Test</h2>";

// Test 1: Cek koneksi
if ($conn->connect_error) {
    echo "<p style='color:red'>❌ Koneksi gagal: " . $conn->connect_error . "</p>";
    exit;
}
echo "<p style='color:green'>✅ Koneksi database berhasil!</p>";

// Test 2: Cek tabel books
$result = $conn->query("SHOW TABLES LIKE 'books'");
if ($result->num_rows > 0) {
    echo "<p style='color:green'>✅ Tabel 'books' ditemukan</p>";
} else {
    echo "<p style='color:red'>❌ Tabel 'books' tidak ditemukan!</p>";
    exit;
}

// Test 3: Hitung jumlah buku
$result = $conn->query("SELECT COUNT(*) as total FROM books");
$row = $result->fetch_assoc();
echo "<p><strong>📚 Total buku di database:</strong> " . $row['total'] . "</p>";

// Test 4: Tampilkan semua buku
// Gunakan COALESCE untuk support kedua nama kolom
$result = $conn->query("SELECT id, title, author, year, 
    COALESCE(category, kategori, 'N/A') as category, 
    stok FROM books ORDER BY id DESC");
if ($result->num_rows > 0) {
    echo "<h3>📖 Daftar Buku:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse:collapse; width:100%;'>";
    echo "<tr style='background:#06BBCC; color:white;'>";
    echo "<th>ID</th><th>Judul</th><th>Penulis</th><th>Kategori</th><th>Stok</th>";
    echo "</tr>";
    
    while ($book = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $book['id'] . "</td>";
        echo "<td>" . $book['title'] . "</td>";
        echo "<td>" . $book['author'] . "</td>";
        echo "<td>" . $book['category'] . "</td>";
        echo "<td>" . $book['stok'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red'>❌ Tidak ada data buku di database!</p>";
    echo "<p>Silakan jalankan query INSERT di phpMyAdmin terlebih dahulu.</p>";
}

// Test 5: Cek API books
echo "<hr><h3>🔌 Test API Books:</h3>";
$api_response = file_get_contents('http://localhost/E-library/api_books.php?action=get_books');
$api_data = json_decode($api_response, true);

if ($api_data['success']) {
    echo "<p style='color:green'>✅ API berfungsi dengan baik!</p>";
    echo "<p>Jumlah buku dari API: " . count($api_data['data']) . "</p>";
} else {
    echo "<p style='color:red'>❌ API error: " . $api_data['message'] . "</p>";
}
?>