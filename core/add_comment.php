<?php
require_once 'config.php';
session_name('USER_SESSION');
session_start();

date_default_timezone_set('Asia/Ho_Chi_Minh');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$post_id = $_POST['post_id'] ?? null;
$content = trim($_POST['content'] ?? '');
$parent_id = $_POST['parent_comment_id'] ?? null;

if (!$post_id || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

$created_at = date('Y-m-d H:i:s');
$updated_at = $created_at;

try {
    $stmt = $conn->prepare("INSERT INTO comments_table 
        (user_id, post_id, content, parent_comment_id, created_at, updated_at)
        VALUES (:user_id, :post_id, :content, :parent_comment_id, :created_at, :updated_at)");

    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':post_id' => $post_id,
        ':content' => $content,
        ':parent_comment_id' => $parent_id,
        ':created_at' => $created_at,
        ':updated_at' => $updated_at
    ]);

// Lấy username người gửi bình luận
$stmt = $conn->prepare("SELECT username FROM users_table WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$sender_username = $stmt->fetchColumn() ?? 'Người dùng';

if (!empty($parent_id)) {
    $stmt = $conn->prepare("SELECT user_id FROM comments_table WHERE id = ?");
    $stmt->execute([$parent_id]);
    $parent_user_id = $stmt->fetchColumn();

    if ($parent_user_id && $parent_user_id != $_SESSION['user_id']) {
        $msg = "🗨️ {$sender_username} đã trả lời bình luận của bạn.";
        $notifyStmt = $conn->prepare("INSERT INTO notifications (user_id, type, content, post_id, comment_id, created_at) 
                                      VALUES (?, 'reply', ?, ?, ?, ?)");
        $notifyStmt->execute([$parent_user_id, $msg, $post_id, $comment_id, $created_at]);
    }
} else {
    $stmt = $conn->prepare("SELECT user_id FROM posts_table WHERE id = ?");
    $stmt->execute([$post_id]);
    $post_owner_id = $stmt->fetchColumn();

    if ($post_owner_id && $post_owner_id != $_SESSION['user_id']) {
        $msg = "🗨️ {$sender_username} đã bình luận vào bài viết của bạn.";
        $notifyStmt = $conn->prepare("INSERT INTO notifications (user_id, type, content, post_id, comment_id, created_at) 
                                      VALUES (?, 'reply', ?, ?, ?, ?)");
        $notifyStmt->execute([$post_owner_id, $msg, $post_id, $comment_id, $created_at]);
    }
}


    echo json_encode(['success' => true, 'message' => 'Comment added successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error', 'error' => $e->getMessage()]);
}
