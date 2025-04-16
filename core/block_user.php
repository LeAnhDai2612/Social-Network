<?php
require_once 'db_functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_name('USER_SESSION');
    session_start();
}

$pdo = connect_to_db();
$blocker_id = $_SESSION['user_id'] ?? null;
$blocked_id = $_POST['blocked_id'] ?? null;
$action = $_POST['action'] ?? 'block'; // Mặc định là block

if ($blocker_id && $blocked_id && $blocker_id != $blocked_id) {
    if ($action === 'block') {
        block_user($pdo, $blocker_id, $blocked_id);
        echo json_encode(['success' => true, 'message' => 'Đã chặn người dùng']);
    } elseif ($action === 'unblock') {
        unblock_user($pdo, $blocker_id, $blocked_id);
        // Nếu gọi từ giao diện unblock, redirect về danh sách block
        if (isset($_POST['redirect']) && $_POST['redirect'] === 'settings') {
            header("Location: ../public_html/index.php");
            exit;
        }
        echo json_encode(['success' => true, 'message' => 'Đã mở chặn người dùng']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
}
