<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(false, 'Method not allowed');
}

$action = $_POST['action'] ?? '';

// ========================================
// REGISTER
// ========================================
if ($action === 'register') {
    $username = clean_input($_POST['username'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validasi
    if (empty($username) || empty($email) || empty($password)) {
        send_json_response(false, 'Semua field harus diisi!');
    }
    
    if (strlen($password) < 6) {
        send_json_response(false, 'Password minimal 6 karakter!');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        send_json_response(false, 'Format email tidak valid!');
    }
    
    // Cek apakah email sudah terdaftar
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();
    
    if ($result->num_rows > 0) {
        send_json_response(false, 'Email sudah terdaftar!');
    }
    
    // Cek apakah username sudah digunakan
    $check_username = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check_username->bind_param("s", $username);
    $check_username->execute();
    $result = $check_username->get_result();
    
    if ($result->num_rows > 0) {
        send_json_response(false, 'Username sudah digunakan!');
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user baru (gunakan username sebagai nama_lengkap)
    $insert = $conn->prepare("INSERT INTO users (nama_lengkap, username, email, password, role) VALUES (?, ?, ?, ?, 'user')");
    $insert->bind_param("ssss", $username, $username, $email, $hashed_password);
    
    if ($insert->execute()) {
        // TIDAK set session - user harus login manual
        send_json_response(true, 'Registrasi berhasil! Silakan login untuk melanjutkan.', [
            'redirect' => 'login.php'
        ]);
    } else {
        send_json_response(false, 'Gagal mendaftar. Silakan coba lagi.');
    }
}

// ========================================
// LOGIN
// ========================================
else if ($action === 'login') {
    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validasi
    if (empty($email) || empty($password)) {
        send_json_response(false, 'Email dan password harus diisi!');
    }
    
    // Cari user berdasarkan email
    $stmt = $conn->prepare("SELECT id, username, email, nama_lengkap, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        send_json_response(false, 'Email tidak terdaftar!');
    }
    
    $user = $result->fetch_assoc();
    
    // Verifikasi password
    if (!password_verify($password, $user['password'])) {
        send_json_response(false, 'Password salah!');
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    $_SESSION['role'] = $user['role'];
    
    // Redirect berdasarkan role
    $redirect = ($user['role'] === 'admin') ? 'admin.php' : 'user.php';
    
    send_json_response(true, 'Login berhasil! Selamat datang kembali.', [
        'redirect' => $redirect
    ]);
}

// ========================================
// INVALID ACTION
// ========================================
else {
    send_json_response(false, 'Action tidak valid!');
}
?>