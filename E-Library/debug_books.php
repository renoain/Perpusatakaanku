<?php
// Simpan file ini sebagai debug_books.php di root folder E-Library
// Akses via: http://localhost/E-library/debug_books.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Debug System E-Library</h1>";
echo "<hr>";

// Test 1: Cek file config.php
echo "<h2>1️⃣ Test Config File</h2>";
if (file_exists('config.php')) {
    echo "✅ File config.php ditemukan<br>";
    require_once 'config.php';
} else {
    echo "❌ File config.php TIDAK ditemukan!<br>";
    echo "📍 Lokasi dicari: " . __DIR__ . "/config.php<br>";
    exit;
}

// Test 2: Cek koneksi database
echo "<h2>2️⃣ Test Database Connection</h2>";
if (isset($conn)) {
    if ($conn->connect_error) {
        echo "❌ Koneksi database GAGAL: " . $conn->connect_error . "<br>";
        exit;
    } else {
        echo "✅ Koneksi database BERHASIL<br>";
        echo "📊 Database: " . $conn->query("SELECT DATABASE()")->fetch_row()[0] . "<br>";
    }
} else {
    echo "❌ Variable \$conn tidak ditemukan!<br>";
    exit;
}

// Test 3: Cek tabel books
echo "<h2>3️⃣ Test Tabel Books</h2>";
$result = $conn->query("SHOW TABLES LIKE 'books'");
if ($result->num_rows > 0) {
    echo "✅ Tabel 'books' ditemukan<br>";
} else {
    echo "❌ Tabel 'books' TIDAK ditemukan!<br>";
    echo "📋 Daftar tabel yang ada:<br>";
    $tables = $conn->query("SHOW TABLES");
    while ($row = $tables->fetch_array()) {
        echo "- " . $row[0] . "<br>";
    }
    exit;
}

// Test 4: Cek struktur tabel
echo "<h2>4️⃣ Struktur Tabel Books</h2>";
$result = $conn->query("DESCRIBE books");
echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
echo "<tr style='background:#06BBCC;color:white;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Test 5: Hitung jumlah buku
echo "<h2>5️⃣ Jumlah Data Buku</h2>";
$result = $conn->query("SELECT COUNT(*) as total FROM books");
$row = $result->fetch_assoc();
$total = $row['total'];
echo "📚 Total buku di database: <strong style='color:green;font-size:24px;'>" . $total . "</strong><br>";

if ($total == 0) {
    echo "<div style='background:#fff3cd;padding:15px;border-left:4px solid #ffc107;margin:10px 0;'>";
    echo "⚠️ <strong>PERINGATAN:</strong> Tidak ada data buku!<br>";
    echo "Silakan jalankan query INSERT di phpMyAdmin terlebih dahulu.";
    echo "</div>";
    exit;
}

// Test 6: Tampilkan semua buku
echo "<h2>6️⃣ Data Buku di Database</h2>";
$query = "SELECT * FROM books ORDER BY id DESC";
$result = $conn->query($query);

if (!$result) {
    echo "❌ Query ERROR: " . $conn->error . "<br>";
    exit;
}

echo "✅ Query berhasil dijalankan<br>";
echo "📖 Jumlah hasil: " . $result->num_rows . " buku<br><br>";

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

echo "<table border='1' cellpadding='10' style='border-collapse:collapse;width:100%;'>";
echo "<tr style='background:#06BBCC;color:white;'>";
echo "<th>ID</th><th>Cover</th><th>Judul</th><th>Penulis</th><th>Tahun</th><th>Kategori</th><th>Stok</th></tr>";

foreach ($books as $book) {
    echo "<tr>";
    echo "<td>" . $book['id'] . "</td>";
    echo "<td><img src='" . $book['cover'] . "' width='50' onerror='this.src=\"https://via.placeholder.com/50x70?text=No+Image\"'></td>";
    echo "<td>" . $book['title'] . "</td>";
    echo "<td>" . $book['author'] . "</td>";
    echo "<td>" . $book['year'] . "</td>";
    echo "<td><span style='background:#06BBCC;color:white;padding:3px 10px;border-radius:5px;'>" . 
         (isset($book['category']) ? $book['category'] : (isset($book['kategori']) ? $book['kategori'] : 'N/A')) . 
         "</span></td>";
    echo "<td><strong>" . $book['stok'] . "</strong></td>";
    echo "</tr>";
}
echo "</table>";

// Test 7: Test API books.php
echo "<h2>7️⃣ Test API Books (api_books.php)</h2>";

if (file_exists('api_books.php')) {
    echo "✅ File api_books.php ditemukan<br>";
    
    // Simulasi API call
    $api_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/api_books.php?action=get_books';
    echo "🔗 API URL: <code>" . $api_url . "</code><br>";
    
    $api_response = @file_get_contents($api_url);
    
    if ($api_response === false) {
        echo "❌ API tidak dapat diakses!<br>";
        echo "⚠️ Error: " . error_get_last()['message'] . "<br>";
    } else {
        echo "✅ API dapat diakses<br>";
        
        $api_data = json_decode($api_response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "❌ Response bukan JSON valid!<br>";
            echo "📄 Raw response:<br>";
            echo "<pre style='background:#f5f5f5;padding:10px;'>" . htmlspecialchars($api_response) . "</pre>";
        } else {
            echo "✅ Response JSON valid<br>";
            
            if (isset($api_data['success']) && $api_data['success']) {
                echo "✅ API mengembalikan success = true<br>";
                echo "📚 Jumlah buku dari API: <strong style='color:green;'>" . count($api_data['data']) . "</strong><br>";
                
                if (count($api_data['data']) > 0) {
                    echo "<br><strong>Sample buku pertama:</strong><br>";
                    echo "<pre style='background:#f5f5f5;padding:10px;'>" . 
                         json_encode($api_data['data'][0], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . 
                         "</pre>";
                }
            } else {
                echo "❌ API mengembalikan success = false<br>";
                echo "📄 Message: " . ($api_data['message'] ?? 'N/A') . "<br>";
            }
        }
    }
} else {
    echo "❌ File api_books.php TIDAK ditemukan!<br>";
}

// Test 8: Test books.php
echo "<h2>8️⃣ Test File books.php</h2>";

if (file_exists('books.php')) {
    echo "✅ File books.php ditemukan<br>";
    
    // Cek apakah ada query di books.php
    $books_content = file_get_contents('books.php');
    if (strpos($books_content, 'SELECT * FROM books') !== false) {
        echo "✅ Query SELECT ditemukan di books.php<br>";
    } else {
        echo "⚠️ Query SELECT tidak ditemukan di books.php<br>";
    }
    
    if (strpos($books_content, 'json_encode($books)') !== false) {
        echo "✅ json_encode(\$books) ditemukan di books.php<br>";
    } else {
        echo "❌ json_encode(\$books) TIDAK ditemukan di books.php<br>";
        echo "⚠️ Kemungkinan variabel \$books tidak di-encode ke JavaScript<br>";
    }
} else {
    echo "❌ File books.php TIDAK ditemukan!<br>";
}

// Test 9: Test books-user.php
echo "<h2>9️⃣ Test File books-user.php</h2>";

if (file_exists('books-user.php')) {
    echo "✅ File books-user.php ditemukan<br>";
    
    $books_user_content = file_get_contents('books-user.php');
    
    if (strpos($books_user_content, 'api_books.php?action=get_books') !== false) {
        echo "✅ Fetch API ditemukan di books-user.php<br>";
    } else {
        echo "❌ Fetch API TIDAK ditemukan di books-user.php<br>";
        echo "⚠️ books-user.php tidak memanggil API untuk load buku<br>";
    }
    
    if (strpos($books_user_content, 'function loadBooks()') !== false) {
        echo "✅ Function loadBooks() ditemukan<br>";
    } else {
        echo "❌ Function loadBooks() TIDAK ditemukan<br>";
    }
    
    if (strpos($books_user_content, 'function displayBooks(') !== false) {
        echo "✅ Function displayBooks() ditemukan<br>";
    } else {
        echo "❌ Function displayBooks() TIDAK ditemukan<br>";
    }
} else {
    echo "❌ File books-user.php TIDAK ditemukan!<br>";
}

// Test 10: JSON Output untuk Testing
echo "<h2>🔟 JSON Data untuk Testing</h2>";
echo "<p>Copy JSON ini untuk test di JavaScript console:</p>";
echo "<textarea style='width:100%;height:150px;font-family:monospace;'>";
echo json_encode(['success' => true, 'data' => $books], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "</textarea>";

echo "<hr>";
echo "<h2>✅ Kesimpulan</h2>";
echo "<ul>";
echo "<li>Database connection: <strong style='color:green;'>OK</strong></li>";
echo "<li>Tabel books: <strong style='color:green;'>OK</strong></li>";
echo "<li>Jumlah buku: <strong style='color:green;'>" . $total . " buku</strong></li>";
echo "<li>API books: " . (file_exists('api_books.php') ? "<strong style='color:green;'>OK</strong>" : "<strong style='color:red;'>NOT FOUND</strong>") . "</li>";
echo "<li>books.php: " . (file_exists('books.php') ? "<strong style='color:green;'>OK</strong>" : "<strong style='color:red;'>NOT FOUND</strong>") . "</li>";
echo "<li>books-user.php: " . (file_exists('books-user.php') ? "<strong style='color:green;'>OK</strong>" : "<strong style='color:red;'>NOT FOUND</strong>") . "</li>";
echo "</ul>";

echo "<div style='background:#d4edda;padding:20px;border-radius:8px;margin:20px 0;border-left:4px solid #28a745;'>";
echo "<h3>📋 Langkah Selanjutnya:</h3>";
echo "<ol>";
echo "<li>Pastikan semua file OK (lihat checklist di atas)</li>";
echo "<li>Buka <strong>books.php</strong> di browser, tekan <kbd>F12</kbd>, cek console</li>";
echo "<li>Buka <strong>books-user.php</strong> di browser, tekan <kbd>F12</kbd>, cek console</li>";
echo "<li>Screenshot error yang muncul di console (jika ada)</li>";
echo "<li>Kirim screenshot ke saya untuk analisa lebih lanjut</li>";
echo "</ol>";
echo "</div>";
?>