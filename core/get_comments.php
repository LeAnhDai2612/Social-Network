<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Ho_Chi_Minh");

require_once '../public_html/partials/post_functions.php';
require_once 'config.php';
session_name('USER_SESSION');

session_start();

header('Content-Type: application/json');

if (!isset($conn)) {
    echo json_encode(['success' => false, 'message' => 'Kết nối DB thất bại']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$post_id = $_GET['post_id'] ?? null;

if (!$post_id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu post_id']);
    exit;
}

try {
    // Lấy bình luận gốc
    $stmt = $conn->prepare("
        SELECT c.id, c.content, c.created_at, c.updated_at, c.user_id, u.username, u.profile_picture_path
        FROM comments_table c
        JOIN users_table u ON c.user_id = u.id
        WHERE c.post_id = :post_id AND c.parent_comment_id IS NULL
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([':post_id' => $post_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy phản hồi
    $stmtReplies = $conn->prepare("
        SELECT c.id, c.content, c.created_at, c.updated_at, c.user_id, c.parent_comment_id, u.username, u.profile_picture_path
        FROM comments_table c
        JOIN users_table u ON c.user_id = u.id
        WHERE c.post_id = :post_id AND c.parent_comment_id IS NOT NULL
        ORDER BY c.created_at ASC
    ");
    $stmtReplies->execute([':post_id' => $post_id]);
    $replies = $stmtReplies->fetchAll(PDO::FETCH_ASSOC);

    // Gắn thông tin cho replies
    foreach ($replies as &$reply) {
        $reply['is_owner'] = ($user_id !== null && $user_id == $reply['user_id']);
        $reply['time_ago'] = get_formatted_time_ago($reply['created_at']);
    }

    // Gắn thông tin cho comments + chèn replies tương ứng
    foreach ($comments as &$comment) {
        $comment['is_owner'] = ($user_id !== null && $user_id == $comment['user_id']);
        $comment['time_ago'] = get_formatted_time_ago($comment['created_at']);

        // Gắn các reply thuộc comment này
        $comment['replies'] = array_values(array_filter($replies, function ($reply) use ($comment) {
            return intval($reply['parent_comment_id']) === intval($comment['id']);
        }));
    }

    echo json_encode(['success' => true, 'comments' => $comments]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi DB', 'error' => $e->getMessage()]);
}
