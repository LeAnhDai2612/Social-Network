
<?php
require_once 'db_functions.php';

header('Content-Type: application/json');

$pdo = connect_to_db();
$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'Thiếu user_id']);
    exit;
}

$following = get_followed_users_by_user($pdo, $user_id);
echo json_encode(['success' => true, 'following' => $following]);
