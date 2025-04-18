<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_name('USER_SESSION');

session_start();

require_once("db_functions.php");

$conn = connect_to_db();

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$result = get_user_by_credentials($conn, $username, $password);

if (!$result) {
    header("Location: login.php?error=Thông tin đăng nhập không đúng");
    exit();
}

// 🔒 Kiểm tra nếu tài khoản bị cấm
if ($result['is_banned'] == 1) {
    header("Location: login.php?error=Tài khoản của bạn đã bị cấm.");
    exit();
}

// ✅ Lưu session nếu đăng nhập hợp lệ và không bị cấm
$_SESSION['user_id'] = $result['id'];
$_SESSION['user_username'] = $result['username'];
$_SESSION['user_full_name'] = $result['full_name'];
$_SESSION['user_email'] = $result['email'];
$_SESSION['user_phone_number'] = $result['phone_number'];
$_SESSION['user_profile_picture_path'] = $result['profile_picture_path'];
$_SESSION['user_display_name'] = $result['display_name'];
$_SESSION['user_bio'] = nl2br(stripslashes($result['bio']));
$_SESSION['user_role'] = $result['role']; 

// ✅ Điều hướng theo vai trò
if ($result['role'] === 'admin') {
    header('Location: admin.php');
} else {
    header('Location: index.php');
}
exit();
