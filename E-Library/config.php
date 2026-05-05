<?php
/**
 * E-Library Configuration File
 * File ini berisi konfigurasi database dan fungsi-fungsi helper
 */

// ========================================
// AUTO LOGOUT & SESSION SETTING
// ========================================
// Start session jika belum - PERBAIKAN: Cek dulu sebelum set parameter
if (session_status() === PHP_SESSION_NONE) {
    // Set timeout session (30 menit)
    ini_set('session.gc_maxlifetime', 1800);
    session_set_cookie_params(1800);
    session_start();
}

// ========================================
// DATABASE CONFIGURATION
// ========================================
// Sesuaikan dengan setting database Anda
define('DB_HOST', 'localhost');      // Host database (biasanya localhost)
define('DB_USER', 'root');           // Username database (default: root)
define('DB_PASS', '');               // Password database (default: kosong untuk XAMPP)
define('DB_NAME', 'perpustakaan');   // Nama database

// ========================================
// SITE CONFIGURATION
// ========================================
if (!defined('SITE_NAME')) define('SITE_NAME', 'E-Library');
if (!defined('SITE_URL')) define('SITE_URL', 'http://localhost/e-library');

// ========================================
// DATABASE CONNECTION
// ========================================
try {
    // Membuat koneksi ke database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Cek koneksi
    if ($conn->connect_error) {
        die("❌ Koneksi database gagal: " . $conn->connect_error);
    }
    
    // Set charset ke UTF-8 untuk support karakter Indonesia
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("❌ Error: " . $e->getMessage());
}

// ========================================
// HELPER FUNCTIONS
// ========================================

/**
 * Membersihkan input dari karakter berbahaya
 * @param string $data - Data yang akan dibersihkan
 * @return string - Data yang sudah dibersihkan
 */
function clean_input($data) {
    $data = trim($data);                    // Hapus spasi di awal dan akhir
    $data = stripslashes($data);            // Hapus backslashes
    $data = htmlspecialchars($data);        // Konversi karakter khusus ke HTML entities
    return $data;
}

/**
 * Cek apakah user sudah login
 * @return bool - True jika sudah login, False jika belum
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Cek apakah user adalah admin
 * @return bool - True jika admin, False jika bukan
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Redirect ke halaman tertentu
 * @param string $url - URL tujuan redirect
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Get user ID yang sedang login
 * @return int|null - User ID atau null jika belum login
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get username yang sedang login
 * @return string|null - Username atau null jika belum login
 */
function get_username() {
    return $_SESSION['username'] ?? null;
}

/**
 * Get email user yang sedang login
 * @return string|null - Email atau null jika belum login
 */
function get_user_email() {
    return $_SESSION['email'] ?? null;
}

/**
 * Get nama lengkap user yang sedang login
 * @return string|null - Nama lengkap atau null jika belum login
 */
function get_user_name() {
    return $_SESSION['nama_lengkap'] ?? null;
}

/**
 * Get role user yang sedang login
 * @return string|null - Role (admin/user) atau null jika belum login
 */
function get_user_role() {
    return $_SESSION['role'] ?? 'user';
}

/**
 * Format tanggal ke format Indonesia
 * @param string $date - Tanggal dalam format Y-m-d
 * @return string - Tanggal dalam format Indonesia
 */
function format_date($date) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = $bulan[date('n', $timestamp)];
    $year = date('Y', $timestamp);
    
    return "$day $month $year";
}

/**
 * Format rupiah
 * @param int $number - Angka yang akan diformat
 * @return string - Format rupiah
 */
function format_rupiah($number) {
    return "Rp " . number_format($number, 0, ',', '.');
}

/**
 * Generate random string
 * @param int $length - Panjang string
 * @return string - Random string
 */
function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Upload file
 * @param array $file - File dari $_FILES
 * @param string $destination - Folder tujuan upload
 * @param array $allowed_types - Tipe file yang diizinkan
 * @return array - ['success' => bool, 'message' => string, 'filename' => string]
 */
function upload_file($file, $destination = 'uploads/', $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf']) {
    // Cek apakah file ada
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File tidak valid'];
    }
    
    // Ambil informasi file
    $file_name = $file['name'];
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Cek ekstensi file
    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan'];
    }
    
    // Cek ukuran file (max 5MB)
    if ($file_size > 5242880) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (max 5MB)'];
    }
    
    // Generate nama file baru
    $new_filename = time() . '_' . generate_random_string(10) . '.' . $file_ext;
    $upload_path = $destination . $new_filename;
    
    // Buat folder jika belum ada
    if (!file_exists($destination)) {
        mkdir($destination, 0777, true);
    }
    
    // Upload file
    if (move_uploaded_file($file_tmp, $upload_path)) {
        return ['success' => true, 'message' => 'File berhasil diupload', 'filename' => $new_filename];
    } else {
        return ['success' => false, 'message' => 'Gagal mengupload file'];
    }
}

/**
 * Delete file
 * @param string $filepath - Path file yang akan dihapus
 * @return bool - True jika berhasil, False jika gagal
 */
function delete_file($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Hitung selisih hari antara dua tanggal
 * @param string $date1 - Tanggal pertama
 * @param string $date2 - Tanggal kedua (default: hari ini)
 * @return int - Selisih hari
 */
function date_difference($date1, $date2 = null) {
    $date2 = $date2 ?? date('Y-m-d');
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    return $interval->days;
}

/**
 * Cek apakah tanggal sudah lewat
 * @param string $date - Tanggal yang dicek
 * @return bool - True jika sudah lewat, False jika belum
 */
function is_date_passed($date) {
    return strtotime($date) < strtotime(date('Y-m-d'));
}

/**
 * Send JSON response
 * @param bool $success - Status success
 * @param string $message - Pesan
 * @param mixed $data - Data tambahan (optional)
 */
function send_json_response($success, $message, $data = null) {
    header('Content-Type: application/json');
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit();
}

/**
 * Get book by ID
 * @param int $book_id - ID buku
 * @return array|null - Data buku atau null jika tidak ditemukan
 */
function get_book_by_id($book_id) {
    global $conn;
    
    $query = "SELECT * FROM buku WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

/**
 * Get user by ID
 * @param int $user_id - ID user
 * @return array|null - Data user atau null jika tidak ditemukan
 */
function get_user_by_id($user_id) {
    global $conn;
    
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

/**
 * Cek apakah buku sedang dipinjam oleh user
 * @param int $user_id - ID user
 * @param int $book_id - ID buku
 * @return bool - True jika sedang dipinjam, False jika tidak
 */
function is_book_borrowed($user_id, $book_id) {
    global $conn;
    
    $query = "SELECT * FROM peminjaman WHERE user_id = ? AND book_id = ? AND status = 'dipinjam'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

/**
 * Cek apakah buku ada di wishlist user
 * @param int $user_id - ID user
 * @param int $book_id - ID buku
 * @return bool - True jika ada di wishlist, False jika tidak
 */
function is_in_wishlist($user_id, $book_id) {
    global $conn;
    
    $query = "SELECT * FROM wishlist WHERE user_id = ? AND book_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

/**
 * Debug function - tampilkan variable dengan format
 * @param mixed $var - Variable yang akan di-debug
 * @param bool $exit - Exit setelah debug (default: false)
 */
function dd($var, $exit = false) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    
    if ($exit) {
        exit();
    }
}

// ========================================
// ERROR REPORTING (Development Mode)
// ========================================
// Uncomment baris di bawah untuk development mode
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// ========================================
// TIMEZONE SETTING
// ========================================
date_default_timezone_set('Asia/Jakarta');

// ========================================
// AUTO LOGOUT - CEK ACTIVITY
// ========================================
// Cek last activity hanya jika user sudah login
if (is_logged_in()) {
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
        // Last activity lebih dari 30 menit yang lalu
        session_unset();
        session_destroy();
        
        // Redirect dengan handling yang lebih baik
        if (!headers_sent()) {
            header('Location: login.php?timeout=1');
            exit();
        }
    }
    $_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time
}

// ========================================
// END OF CONFIG
// ========================================
?>