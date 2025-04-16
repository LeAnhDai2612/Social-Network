<?php
require_once 'db_functions.php';

function execute($params)
{
    $pdo = connect_to_db();
    $params = json_decode($params, true);

    $like_action = isset($params['like_action']) ? trim($params['like_action']) : '';
    $user_id = isset($params['user_id']) ? trim($params['user_id']) : '';
    $post_id = isset($params['post_id']) ? trim($params['post_id']) : '';

    if (empty($user_id) || empty($post_id)) {
        return ['success' => false, 'error' => 'Invalid request: missing user_id or post_id parameter'];
    }

    $success = $like_action === 'add'
        ? add_like($pdo, $user_id, $post_id)
        : remove_like($pdo, $user_id, $post_id);

    // Nếu like thành công và là hành động "add", thì tạo thông báo
    if ($success && $like_action === 'add') {
        try {
            // Lấy user_id của chủ bài viết
            $stmt = $pdo->prepare("SELECT user_id FROM posts_table WHERE id = ?");
            $stmt->execute([$post_id]);
            $post_owner_id = $stmt->fetchColumn();

            if ($post_owner_id && $post_owner_id != $user_id) {
                $content = "Người dùng ID $user_id đã thích bài viết của bạn.";
                $notifyStmt = $pdo->prepare("INSERT INTO notifications (user_id, type, content, post_id) VALUES (?, 'like', ?, ?)");
                $notifyStmt->execute([$post_owner_id, $content, $post_id]);

            }
        } catch (Exception $e) {
            // Ghi log hoặc xử lý nếu cần
        }
    }

    if ($success) {
        return ['success' => true];
    }
    return ['success' => false, 'error' => 'Unable to add the like'];
}
?>
