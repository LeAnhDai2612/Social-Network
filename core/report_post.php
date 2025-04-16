<?php
require_once("db_functions.php");
session_name('USER_SESSION');

session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;
$reason = $_POST['reason'] ?? null;
$description = $_POST['description'] ?? '';

if (!$post_id || !$reason) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin báo cáo']);
    exit;
}

$pdo = connect_to_db();

$stmt = $pdo->prepare("INSERT INTO reports_table (post_id, reporter_id, reason, description, report_time) VALUES (?, ?, ?, ?, NOW())");
if ($stmt->execute([$post_id, $user_id, $reason, $description])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không thể gửi báo cáo']);
}
