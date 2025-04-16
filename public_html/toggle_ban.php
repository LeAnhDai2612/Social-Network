<?php
require_once '../core/admin_only.php';
require_once '../core/db_functions.php';

$conn = connect_to_db();

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!$id || !in_array($action, ['ban', 'unban'])) {
    header("Location: admin.php?error=invalid_request");
    exit();
}

// Không cho admin tự ban chính mình
if ($id == $_SESSION['user_id']) {
    header("Location: admin.php?error=cannot_ban_self");
    exit();
}

$isBanned = $action === 'ban' ? 1 : 0;

$stmt = $conn->prepare("UPDATE users_table SET is_banned = ? WHERE id = ?");
$stmt->execute([$isBanned, $id]);

header("Location: admin.php?success=status_updated");
exit();
