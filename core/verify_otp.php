<?php
require_once 'db_functions.php';
session_name('USER_SESSION');

session_start();

$conn = connect_to_db();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: verify_otp.php?error=Phương thức không hợp lệ.");
    exit;
}

$email = $_POST['email'] ?? '';
$otp = $_POST['otp_code'] ?? '';

if (empty($email) || empty($otp)) {
    header("Location: verify_otp.php?error=Thiếu email hoặc mã OTP.");
    exit;
}

// Lấy thông tin OTP
$stmt = $conn->prepare("SELECT * FROM otp_table WHERE email = ? AND otp_code = ? AND verified = 0 LIMIT 1");
$stmt->execute([$email, $otp]);
$otpData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$otpData) {
    header("Location: verify_otp.php?error=Mã OTP không đúng hoặc đã được xác thực.");
    exit;
}

if (strtotime($otpData['expires_at']) < time()) {
    header("Location: verify_otp.php?error=Mã OTP đã hết hạn.");
    exit;
}

// Cập nhật trạng thái đã xác thực
$stmt = $conn->prepare("UPDATE otp_table SET verified = 1 WHERE id = ?");
$stmt->execute([$otpData['id']]);

// ======== Trường hợp RESET MẬT KHẨU ========
if (isset($_SESSION['reset_target_email']) && $_SESSION['reset_target_email'] === $email) {
    $_SESSION['reset_password_verified'] = true;
    $_SESSION['resetPasswordMode'] = true;
    header("Location: login.php");
    exit;
}

// ======== Trường hợp ĐĂNG KÝ (pending_user) ========
if (!isset($_SESSION['pending_user'])) {
    header("Location: register.php?error=Session hết hạn, vui lòng đăng ký lại.");
    exit;
}

$user = $_SESSION['pending_user'];

$stmt = $conn->prepare("INSERT INTO users_table 
    (username, full_name, email, phone_number, password, profile_picture_path, display_name, bio, is_verified, role)
    VALUES (?, ?, ?, ?, ?, '', ?, '', 1, ?)");


$stmt->execute([
    $user['username'],
    $user['full_name'],
    $user['email'],
    $user['phone_number'],
    $user['hashed_password'],
    $user['full_name'],
    $user['role'] ?? 'user'
]);

$user_id = $conn->lastInsertId();

// Set session người dùng
$_SESSION['user_id'] = $user_id;
$_SESSION['email'] = $user['email'];
$_SESSION['user_username'] = $user['username'];
$_SESSION['user_full_name'] = $user['full_name'];
$_SESSION['user_phone_number'] = $user['phone_number'];
$_SESSION['user_display_name'] = $user['full_name'];
$_SESSION['user_bio'] = '';
$_SESSION['user_profile_picture_path'] = '';
$_SESSION['user_role'] = $user['role'] ?? 'user';


unset($_SESSION['pending_user']);
$_SESSION['registration_complete'] = true;

header("Location: ../public_html/profile_setup.php");
exit;
