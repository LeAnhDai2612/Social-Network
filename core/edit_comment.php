<?php
require_once 'config.php';
session_name('USER_SESSION');

session_start();

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$comment_id = $_POST['comment_id'] ?? null;
$new_content = trim($_POST['content'] ?? '');

if (!$comment_id || empty($new_content)) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

try {
    // Kiểm tra quyền chỉnh sửa
    $stmt = $conn->prepare("SELECT user_id FROM comments_table WHERE id = :id");
    $stmt->execute([':id' => $comment_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$comment) {
        echo json_encode(['success' => false, 'message' => 'Comment not found']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $is_author = $user_id == $comment['user_id'];

    if (!$is_author) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        exit;
    }

    // Cập nhật nội dung
    $update = $conn->prepare("UPDATE comments_table SET content = :content, updated_at = NOW() WHERE id = :id");
    $update->execute([
        ':content' => $new_content,
        ':id' => $comment_id
    ]);

    echo json_encode(['success' => true, 'message' => 'Comment updated']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error', 'error' => $e->getMessage()]);
}
?>
