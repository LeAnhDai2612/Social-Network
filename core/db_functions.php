<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Ho_Chi_Minh');
use Cloudinary\Api\Upload\UploadApi;
require_once 'config.php';

function connect_to_db() {
    require_once 'config.php';
    $dsn = "mysql:host=" . DB_CONFIG['host'] . ";dbname=" . DB_CONFIG['db'];
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'",
    ];

    try {
        return new PDO($dsn, DB_CONFIG['user'], DB_CONFIG['pass'], $options);
    } catch (PDOException $e) {
        exit("Connection failed: " . $e->getMessage());
    }
}
function upload_image_to_cloudinary($file, $target_dir, $public_id = null)
{
    $file_type = $file['type'] ?? 'image/jpeg';
    $resource_type = str_starts_with($file_type, 'video/') ? 'video' : 'image';

    $options = [
        'resource_type' => $resource_type,
        'overwrite' => true,
        'invalidate' => true,
    ];

    // ✅ Auto sanitize and shorten public_id
    if (!empty($public_id)) {
        // Giữ lại chỉ tên file (tránh lặp folder)
        $basename = basename($public_id);
        $basename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $basename);
        $basename = substr($basename, 0, 40); // Giới hạn max 40 ký tự
        $options['public_id'] = "$target_dir/$basename";
    } else {
        // Tạo tên ngẫu nhiên nếu không truyền vào
        $timestamp = time();
        $random = bin2hex(random_bytes(4)); // 8 ký tự hex
        $options['public_id'] = "$target_dir/{$timestamp}_{$random}";
    }

    error_log("Cloudinary Upload Options: " . print_r($options, true));
    error_log("Uploading file: " . $file['tmp_name']);

    try {
        $uploadResult = (new UploadApi())->upload($file['tmp_name'], $options);
        error_log("Cloudinary Upload Success: " . print_r($uploadResult, true));
        return $uploadResult;
    } catch (\Exception $e) {
        error_log("Cloudinary Upload Error: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());
        return null;
    }
}


function process_file_and_execute_query($pdo, $file, $target_dir, $query_callback)
{
    if (empty($file['name'])) {
        return false;
    }

    $new_image_result = upload_image_to_cloudinary($file, $target_dir);
    $new_image_path = $new_image_result['secure_url'];
    $new_image_public_id = $new_image_result['public_id'];

    if (!$new_image_path || !$new_image_public_id) {
        return false;
    }

    return $query_callback($pdo, $new_image_path, $new_image_public_id);
}

function create_user($pdo, $email, $phone_number, $full_name, $username, $hashed_password, $display_name, $bio)
{
    $target_dir = 'socialnetwork/profile-pictures/';

    $query_callback = function ($pdo, $profile_picture_path, $new_image_public_id) use ($username, $full_name, $email, $phone_number, $hashed_password, $display_name, $bio) {
        $username = strtolower($username);
        $sql = "INSERT INTO users_table 
                  (username, full_name, email, phone_number, password, profile_picture_path, image_public_id, display_name, bio) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $full_name, $email, $phone_number, $hashed_password, $profile_picture_path, $new_image_public_id, $display_name, $bio]);

        return $stmt->rowCount() > 0;
    };

    return process_file_and_execute_query($pdo, $_FILES['profile_picture_picker'], $target_dir, $query_callback);
}
function add_post($pdo, $user_id, $caption, $created_at, $privacy = 'public', $allowed_viewers = [])
{
    $target_dir = 'socialnetwork/posts';

    $query_callback = function ($pdo, $media_path, $public_id)
        use ($user_id, $caption, $created_at, $privacy, $allowed_viewers) {

        $allowed_viewers_str = empty($allowed_viewers) ? null : implode(',', $allowed_viewers);

        $sql = "INSERT INTO posts_table (user_id, media_path, image_public_id, caption, created_at, privacy, allowed_viewers) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $user_id,
            $media_path,
            $public_id,
            $caption,
            $created_at,
            $privacy,
            $allowed_viewers_str
        ]);

        return $stmt->rowCount() > 0;
    };

    return process_file_and_execute_query($pdo, $_FILES['post_modal_media_picker'], $target_dir, $query_callback);
}




function get_user_by_credentials($pdo, $username, $password)
{

    $sql = "SELECT * 
              FROM users_table 
              WHERE username = ?
                OR email = ?
                OR phone_number = ?;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $username,
        $username,
        $username
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return false;
    }

    $hashed_password = $row['password'];

    if (password_verify($password, $hashed_password)) {
        return $row;
    }

    return false;
}

function get_user_info_from_username($pdo, $username)
{

    $sql = "SELECT * 
              FROM users_table 
              WHERE username = ?
                OR email = ?
                OR phone_number = ?;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $username,
        $username,
        $username
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return false;
    }

    return $row;
}

function fetch_posts($pdo, $sql)
{
    $stmt = $pdo->query($sql);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $posts;
}

function get_all_posts($pdo, $current_user_id = null) {
    $query = "SELECT p.*, u.username, u.display_name, u.profile_picture_path
              FROM posts_table p
              JOIN users_table u ON p.user_id = u.id
              WHERE 1";

    $params = [];

    if ($current_user_id) {
        // Ẩn bài viết của người đã bị block và người block mình
        $query .= " AND p.user_id NOT IN (
                        SELECT blocked_id FROM blocked_users WHERE blocker_id = ?
                        UNION
                        SELECT blocker_id FROM blocked_users WHERE blocked_id = ?
                   )";
        $params[] = $current_user_id;
        $params[] = $current_user_id;
    }

    $query .= " ORDER BY p.created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function get_user_posts($pdo, $user_id)
{
    $sql = "SELECT p.*, u.username, u.display_name, u.profile_picture_path
              FROM posts_table AS p
              JOIN users_table AS u ON p.user_id = u.id
              WHERE u.id = $user_id
              ORDER BY p.created_at DESC;
              ";

    return fetch_posts($pdo, $sql);
}

function get_user_post_count($pdo, $user_id)
{
    $sql = "SELECT COUNT(*) AS post_count FROM posts_table WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC)['post_count'];
    return $row;
}

function get_all_users($pdo)
{
    $sql = "SELECT id, username, display_name, profile_picture_path FROM users_table";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $profiles = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $profiles[] = $row;
    }

    return $profiles;
}

function get_row_by_id($pdo, $table_name, $row_id)
{
    $sql = "SELECT * 
              FROM $table_name
              WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$row_id]);

    if (!$stmt || $stmt->rowCount() === 0) {
        return false;
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row;
}

function get_user_info($pdo, $user_id)
{
    return get_row_by_id($pdo, 'users_table', $user_id);
}

function get_users_info($pdo, $user_ids)
{
    $placeholders = implode(',', array_fill(0, count($user_ids), '?'));
    $sql = "SELECT id, username, display_name, profile_picture_path 
            FROM users_table 
            WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($user_ids);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_post($pdo, $post_id)
{
    return get_row_by_id($pdo, 'posts_table', $post_id);
}

function update_post($pdo, $post_id, $new_caption)
{
    $new_image_file = $_FILES['post_modal_image_picker'];
    $new_image_path = null;
    $is_image_updated = !empty($new_image_file['name']);
    $is_caption_updated = isset($new_caption);
    $is_post_updated = $is_caption_updated || $is_image_updated;

    if ($is_post_updated) {
        if ($is_caption_updated && $is_image_updated) {
            $target_dir = 'socialnetwork/posts';
            $image_public_id = get_image_public_id_from_post($pdo, $post_id);
            $new_image_path = upload_image_to_cloudinary($new_image_file, $target_dir, $image_public_id)['secure_url'];

            $sql = "UPDATE posts_table SET 
              image_dir = ?,
              caption = ?,
              updated_at = NOW()
              WHERE id = ?";

            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$new_image_path, $new_caption, $post_id]);
        }
        if ($is_caption_updated) {
            $sql = "UPDATE posts_table SET 
              caption = ?,
              updated_at = NOW()
              WHERE id = ?";

            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$new_caption, $post_id]);
        }
        if ($is_image_updated) {
            $target_dir = 'socialnetwork/posts';
            $image_public_id = get_image_public_id_from_post($pdo, $post_id);
            $new_image_path = upload_image_to_cloudinary($new_image_file, $target_dir, $image_public_id)['secure_url'];

            $sql = "UPDATE posts_table SET 
              image_dir = ?,
              updated_at = NOW()
              WHERE id = ?";

            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$new_image_path, $post_id]);
        }
    }
    return true;
}

function update_user_profile(PDO $pdo, int $user_id, array $update_data): bool
{
    if (empty($update_data)) {
        return true;
    }

    $allowed_columns = ['full_name', 'display_name', 'username', 'bio', 'profile_picture_path', 'image_public_id'];
    $set_parts = [];
    $params = [];

    foreach ($update_data as $column => $value) {
        if (in_array($column, $allowed_columns)) {
            $placeholder = ":" . $column;
            $set_parts[] = "`" . $column . "` = " . $placeholder;
            $params[$placeholder] = $value;
        } else {
            error_log("Cảnh báo: Cố gắng cập nhật cột không được phép: " . $column . " cho user ID: " . $user_id);
        }
    }

    if (empty($set_parts)) {
        error_log("Không có cột hợp lệ nào được cung cấp để cập nhật cho user ID: " . $user_id);
        return false;
    }

    $sql = "UPDATE users_table SET " . implode(', ', $set_parts) . " WHERE id = :user_id";
    $params[':user_id'] = $user_id;

    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Lỗi cập nhật hồ sơ user ID $user_id: " . $e->getMessage());
        if ($e->errorInfo[1] == 1062) {
            throw new Exception("Tên người dùng hoặc thông tin khác đã tồn tại.");
        }
        return false;
    }
}


if (!function_exists('is_username_taken')) {
    function is_username_taken(PDO $pdo, string $username, int $exclude_user_id): bool
    {
        $sql = "SELECT 1 FROM users_table WHERE username = :username AND id != :exclude_user_id LIMIT 1";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':exclude_user_id', $exclude_user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() !== false;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra username '$username': " . $e->getMessage());
            return true;
        }
    }
}


if (!function_exists('get_image_public_id_from_user')) {
    function get_image_public_id_from_user($pdo, $user_id)
    {
        $sql = "SELECT image_public_id FROM users_table WHERE id = ?";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id]);
            $result = $stmt->fetchColumn();
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Lỗi lấy image public id từ user $user_id: " . $e->getMessage());
            return null;
        }
    }
}

function delete_image_from_cloudinary($public_id)
{
    require_once '../vendor/autoload.php';

    try {
        $result = (new Cloudinary\Api\Upload\UploadApi())->destroy($public_id);
        return !empty($result) && $result['result'] == 'ok';
    } catch (\Exception $e) {
        error_log("Lỗi xoá ảnh Cloudinary: " . $e->getMessage());
        return false;
    }
    
}

function get_image_public_id_from_user($pdo, $user_id)
{
    // Thay 'users_table' bằng tên bảng người dùng chính xác của bạn nếu khác
    $sql = "SELECT image_public_id FROM users_table WHERE id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['image_public_id'] ?? null;
    } catch (\PDOException $e) {
        error_log("Database Error in get_image_public_id_from_user: " . $e->getMessage());
        return null; // Trả về null nếu có lỗi DB
    }
}

function get_image_public_id_from_post($pdo, $post_id)
{
    $stmt = $pdo->prepare("SELECT cloudinary_public_id, image_public_id FROM posts_table WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) return null;

    // Ưu tiên cột mới
    if (!empty($post['cloudinary_public_id'])) {
        return $post['cloudinary_public_id'];
    }

    // Hỗ trợ tạm cột cũ nếu chưa migrate hoàn toàn
    if (!empty($post['image_public_id'])) {
        return $post['image_public_id'];
    }

    return null;
}


function delete_post($pdo, $post_id)
{
    $sql = "DELETE FROM posts_table WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id]);

    return $stmt->rowCount() > 0;
}

function delete_post_with_image($pdo, $post_id)
{
    $image_public_id = get_image_public_id_from_post($pdo, $post_id);

    $image_deleted = delete_image_from_cloudinary($image_public_id);

    if (!$image_deleted) {
        return false;
    }

    $post_deleted = delete_post($pdo, $post_id);

    if (!$image_deleted) {
        error_log("Cảnh báo: Không xoá được ảnh từ Cloudinary với ID: " . $image_public_id);
    }

    return true;
}

function does_value_exist($pdo, $table, $column, $value)
{
    $sql = "SELECT * FROM $table WHERE $column = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$value]);
    $result = $stmt->fetchAll();
    return count($result) > 0;
}

function does_row_exist($pdo, $table, $column1, $value1, $column2, $value2)
{
    $sql = "SELECT * FROM $table WHERE $column1 = ? AND $column2 = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$value1, $value2]);
    $result = $stmt->fetchAll();
    return count($result) > 0;
}

function add_like($pdo, $liker_id, $post_id)
{
    $sql = "INSERT INTO likes_table (liker_id, post_id) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$liker_id, $post_id]);

    return $stmt->rowCount() > 0;
}

function remove_like($pdo, $liker_id, $post_id)
{
    $sql = "DELETE FROM likes_table WHERE liker_id = ? AND post_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$liker_id, $post_id]);

    return $stmt->rowCount() > 0;
}

function get_post_likes($pdo, $post_id)
{
    $sql = "SELECT u.*
            FROM users_table AS u
            JOIN likes_table AS l ON u.id = l.liker_id
            WHERE l.post_id = ?
            ORDER BY liked_at;
            ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$post_id]);

    $profiles = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $profiles[] = $row;
    }

    return $profiles;
}

function get_user_followers(PDO $pdo, $user_id)
{
    $sql = "SELECT u.* 
            FROM users_table u 
            INNER JOIN followers_table f ON u.id = f.follower_id
            WHERE f.followed_id = :user_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_followed_users_by_user(PDO $pdo, $user_id)
{
    $sql = "SELECT u.* 
            FROM users_table u 
            INNER JOIN followers_table f ON u.id = f.followed_id
            WHERE f.follower_id = :user_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function follow_user(PDO $pdo, $follower_id, $followed_id)
{
    $sql = "INSERT INTO followers_table (follower_id, followed_id) VALUES (:follower_id, :followed_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':follower_id', $follower_id, PDO::PARAM_INT);
    $stmt->bindParam(':followed_id', $followed_id, PDO::PARAM_INT);
    $success = $stmt->execute();
    return $success;
}

function unfollow_user(PDO $pdo, $follower_id, $followed_id)
{
    $sql = "DELETE FROM followers_table WHERE follower_id = :follower_id AND followed_id = :followed_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':follower_id', $follower_id, PDO::PARAM_INT);
    $stmt->bindParam(':followed_id', $followed_id, PDO::PARAM_INT);
    $success = $stmt->execute();
    return $success;
}






function block_user($pdo, $blocker_id, $blocked_id) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO blocked_users_table (blocker_id, blocked_id) VALUES (?, ?)");
    return $stmt->execute([$blocker_id, $blocked_id]);
}

function unblock_user($pdo, $blocker_id, $blocked_id) {
    $stmt = $pdo->prepare("DELETE FROM blocked_users_table WHERE blocker_id = ? AND blocked_id = ?");
    return $stmt->execute([$blocker_id, $blocked_id]);
}

function is_user_blocked($pdo, $blocker_id, $blocked_id) {
    $stmt = $pdo->prepare("SELECT 1 FROM blocked_users_table WHERE blocker_id = ? AND blocked_id = ?");
    $stmt->execute([$blocker_id, $blocked_id]);
    return $stmt->fetchColumn() ? true : false;
}

function get_blocked_users($pdo, $blocker_id) {
    $stmt = $pdo->prepare("SELECT u.id, u.display_name, u.username, u.profile_picture_path FROM users_table u
                           JOIN blocked_users_table b ON u.id = b.blocked_id
                           WHERE b.blocker_id = ?");
    $stmt->execute([$blocker_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



?>