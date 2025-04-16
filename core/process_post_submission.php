<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (session_status() === PHP_SESSION_NONE) {
    session_name('USER_SESSION');
    session_start();
}

require_once '../core/db_functions.php';
require_once '../core/utility_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$pdo = connect_to_db();
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo "Bạn chưa đăng nhập.";
    exit;
}

$caption = trim($_POST['post_caption'] ?? '');
if (strlen($caption) > 99999) {
    echo "Caption quá dài.";
    exit;
}

$privacy = $_POST['privacy'] ?? 'public';
$allowed_viewers = $_POST['allowed_viewers'] ?? [];
$allowed_viewers_str = !empty($allowed_viewers) ? implode(',', $allowed_viewers) : null;

$file = $_FILES['post_modal_media_picker'] ?? null;
if (!$file || $file['error'] !== 0) {
    echo "Không có file nào được chọn hoặc có lỗi xảy ra.";
    exit;
}

$allowed_image_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/bmp'];
$allowed_video_types = ['video/mp4', 'video/webm'];
$media_type = '';

if (in_array($file['type'], $allowed_image_types)) {
    $media_type = 'image';
} elseif (in_array($file['type'], $allowed_video_types)) {
    $media_type = 'video';
} else {
    echo "Định dạng file không được hỗ trợ.";
    exit;
}

$upload_result = upload_image_to_cloudinary($file, "posts");

$secure_url = $upload_result['secure_url'] ?? '';
$public_id = $upload_result['public_id'] ?? '';

if (!$secure_url) {
    echo "Upload thất bại.";
    exit;
}

$stmt = $pdo->prepare("INSERT INTO posts_table 
    (user_id, caption, media_path, media_type, cloudinary_public_id, created_at, privacy, allowed_viewers)
    VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");

$stmt->execute([
    $user_id,
    $caption,
    $secure_url,
    $media_type,
    $public_id,
    $privacy,
    $allowed_viewers_str
]);

header("Location: index.php");
exit;
