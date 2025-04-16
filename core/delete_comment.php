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
if (!$comment_id) {
    echo json_encode(['success' => false, 'message' => 'Missing comment ID']);
    exit;
}

try {
    // Kiểm tra người dùng có phải là người viết bình luận hoặc admin không
    $stmt = $conn->prepare("SELECT user_id FROM comments_table WHERE id = :id");
    $stmt->execute([':id' => $comment_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$comment) {
        echo json_encode(['success' => false, 'message' => 'Comment not found']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $is_author = $user_id == $comment['user_id'];

    // Nếu không phải người tạo bình luận, cần thêm kiểm tra quyền admin nếu có
    if (!$is_author /* && !isAdmin($user_id) */) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        exit;
    }

    // Xoá bình luận chính (có thể kèm xoá phản hồi nếu muốn)
    $delete = $conn->prepare("DELETE FROM comments_table WHERE id = :id OR parent_comment_id = :id");
    $delete->execute([':id' => $comment_id]);

    echo json_encode(['success' => true, 'message' => 'Comment deleted']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error', 'error' => $e->getMessage()]);
}
?>
