<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_name('USER_SESSION');
    session_start();
}

require_once 'db_functions.php';
require_once 'utility_functions.php';

if (!isset($_SESSION['user_id'])) {
    // Trả về lỗi 401 Unauthorized sẽ phù hợp hơn
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$current_user_id = $_SESSION['user_id'];
$pdo = connect_to_db();

try {
    $sql = "SELECT u.id, u.username, u.display_name, u.profile_picture_path
            FROM users_table u
            JOIN followers_table f ON u.id = f.followed_id
            LEFT JOIN blocked_users_table b1 ON u.id = b1.blocker_id AND b1.blocked_id = :current_user_id_for_blocker_check
            LEFT JOIN blocked_users_table b2 ON u.id = b2.blocked_id AND b2.blocker_id = :current_user_id_for_blocked_check
            WHERE f.follower_id = :current_user_id_in_follow
              AND b1.blocker_id IS NULL -- Người theo dõi không chặn tôi
              AND b2.blocked_id IS NULL -- Tôi không chặn người theo dõi
              AND u.id != :current_user_id_self_check -- Loại trừ chính mình
            ORDER BY u.display_name ASC";

    $stmt = $pdo->prepare($sql);

    // Bind tất cả các placeholder với cùng giá trị user_id
    $stmt->bindParam(':current_user_id_for_blocker_check', $current_user_id, PDO::PARAM_INT);
    $stmt->bindParam(':current_user_id_for_blocked_check', $current_user_id, PDO::PARAM_INT);
    $stmt->bindParam(':current_user_id_in_follow', $current_user_id, PDO::PARAM_INT);
    $stmt->bindParam(':current_user_id_self_check', $current_user_id, PDO::PARAM_INT);

    $stmt->execute();
    $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($suggestions as &$user) {
        // Chỉ tạo key 'name' nếu 'display_name' tồn tại và không rỗng
        $user['name'] = !empty($user['display_name']) ? $user['display_name'] : $user['username'];
    }
    unset($user);

    echo json_encode($suggestions);

} catch (PDOException $e) {
    // Log lỗi chi tiết hơn có thể giúp ích
    error_log("PDO Error fetching follow suggestions for user $current_user_id: " . $e->getMessage() . " | SQL: " . $sql);
    // Trả về lỗi 500 Internal Server Error sẽ phù hợp hơn
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred.']);
} catch (Exception $e) {
    error_log("General error fetching follow suggestions for user $current_user_id: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error occurred.']);
}

?>