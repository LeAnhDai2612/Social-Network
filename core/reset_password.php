<?php
require_once 'db_functions.php';
session_name('USER_SESSION');

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php?error=Phương thức không hợp lệ.");
    exit;
}

// Kiểm tra xem đã xác thực OTP chưa
if (!isset($_SESSION['reset_password_verified']) || !$_SESSION['reset_password_verified'] || !isset($_SESSION['reset_target_email'])) {
    header("Location: login.php?error=Bạn chưa xác thực OTP.");
    exit;
}

$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($new_password) || empty($confirm_password)) {
    header("Location: login.php?show_reset_password=1&error=Vui lòng điền đầy đủ thông tin.");
    exit;
}

if ($new_password !== $confirm_password) {
    header("Location: login.php?show_reset_password=1&error=Mật khẩu xác nhận không khớp.");
    exit;
}

if (strlen($new_password) < 6) {
    header("Location: login.php?show_reset_password=1&error=Mật khẩu phải có ít nhất 6 ký tự.");
    exit;
}

// Mã hoá mật khẩu mới
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$email = $_SESSION['reset_target_email'];

try {
    $conn = connect_to_db();
    $stmt = $conn->prepare("UPDATE users_table SET password = ? WHERE email = ?");
    $stmt->execute([$hashed_password, $email]);

    // Huỷ session xác thực
    unset($_SESSION['reset_target_email']);
    unset($_SESSION['reset_password_verified']);

    header("Location: login.php?success=Đặt lại mật khẩu thành công. Bạn có thể đăng nhập ngay.");
    exit;
} catch (PDOException $e) {
    header("Location: login.php?show_reset_password=1&error=Lỗi hệ thống: " . $e->getMessage());
    exit;
}
