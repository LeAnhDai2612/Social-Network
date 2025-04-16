<?php
require_once '../core/admin_only.php';
require_once '../core/db_functions.php';

$conn = connect_to_db();
$postId = $_GET['id'] ?? null;

if (!$postId) {
    header("Location: admin.php?error=invalid_id");
    exit();
}

try {
    // Xoá report trước
    $stmt1 = $conn->prepare("DELETE FROM reports_table WHERE post_id = ?");
    $stmt1->execute([$postId]);

    // Xoá bài viết
    $stmt2 = $conn->prepare("DELETE FROM posts_table WHERE id = ?");
    $stmt2->execute([$postId]);

    header("Location: admin.php?success=deleted");
    exit();
} catch (PDOException $e) {
    header("Location: admin.php?error=delete_failed");
    exit();
}

