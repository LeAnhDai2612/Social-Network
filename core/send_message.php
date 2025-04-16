<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_name('USER_SESSION');
    session_start();
}

require_once 'db_functions.php';
require_once 'utility_functions.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'] ?? null;
$content = trim($_POST['content'] ?? '');
$media_path = null;
$media_type = null;

if (!$receiver_id && !$content && empty($_FILES['media'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Thiếu thông tin cần thiết']);
    exit;
}

try {
    // Xử lý upload file nếu có
    if (!empty($_FILES['media']) && $_FILES['media']['error'] === 0) {
        $upload_dir = "../uploads/messages/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = time() . '_' . basename($_FILES['media']['name']);
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['media']['tmp_name'], $target_file)) {
            $media_path = $target_file;
            $media_type = mime_content_type($target_file);
        }
    }

    // Lưu tin nhắn
    $conn = connect_to_db();
    $stmt = $conn->prepare("
        INSERT INTO messages_table (sender_id, receiver_id, content, media_path, media_type)
        VALUES (?, ?, ?, ?, ?)
    ");
    $success = $stmt->execute([
        $sender_id,
        $receiver_id,
        $content ?: null,
        $media_path,
        $media_type
    ]);

    if ($success) {
        echo json_encode(['success' => true, 'message_id' => $conn->lastInsertId()]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Không thể gửi tin nhắn']);
    }
} catch (PDOException $e) {
    error_log("Gửi tin nhắn lỗi: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Lỗi máy chủ khi gửi tin nhắn']);
}
