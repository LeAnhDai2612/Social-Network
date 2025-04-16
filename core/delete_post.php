<?php
require_once 'db_functions.php';
require_once '../vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Exception\ApiError;

if (session_status() === PHP_SESSION_NONE) {
    session_name('USER_SESSION');
    session_start();
}

function execute($params)
{
    $pdo = connect_to_db();
    $params = json_decode($params, true);

    $post_id = $params['postId'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$post_id || !$user_id) {
        http_response_code(400);
        return ['success' => false, 'error' => 'Dữ liệu không hợp lệ.'];
    }

    $stmt = $pdo->prepare("SELECT user_id, cloudinary_public_id, media_type FROM posts_table WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        http_response_code(404);
        return ['success' => false, 'error' => 'Không tìm thấy bài viết.'];
    }

    if ($post['user_id'] != $user_id) {
        http_response_code(403);
        return ['success' => false, 'error' => 'Bạn không có quyền xoá bài viết này.'];
    }

    $public_id = $post['cloudinary_public_id'] ?? null;
    $resource_type = $post['media_type'] ?? 'image';
    $cloudinary_deleted = true;

    if (!empty($public_id)) {
        try {
            Configuration::instance([
                'cloud' => ['cloud_name' => '', 'api_key' => '', 'api_secret' => ''],
                'url' => ['secure' => true]
            ]);
            $api = new AdminApi();
            $result = $api->deleteAssets([$public_id], ["resource_type" => $resource_type, "invalidate" => true]);
            if (isset($result['deleted'][$public_id]) && $result['deleted'][$public_id] === 'deleted') {
                $cloudinary_deleted = true;
            } else {
                $cloudinary_deleted = false;
            }
        } catch (ApiError $e) {
            $cloudinary_deleted = false;
        } catch (\Exception $e) {
            $cloudinary_deleted = false;
        }
    }

    $db_deleted = false;
    if ($cloudinary_deleted) {
        try {
            $sql = "DELETE FROM posts_table WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$post_id])) {
                $db_deleted = $stmt->rowCount() > 0;
            }
        } catch (\PDOException $e) {
            $db_deleted = false;
        }
    }

    if ($db_deleted) {
        return ['success' => true];
    } else {
        http_response_code(500);
        if (!$cloudinary_deleted) {
            return ['success' => false, 'error' => 'Lỗi khi xóa file media. Không thể hoàn tất xóa bài đăng.'];
        } else {
            return ['success' => false, 'error' => 'Lỗi khi xóa bài đăng khỏi cơ sở dữ liệu.'];
        }
    }
}
?>
