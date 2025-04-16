<?php
require_once '../core/admin_only.php';
require_once '../core/db_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];
    $conn = connect_to_db();

    $stmt = $conn->prepare("DELETE FROM comments_table WHERE id = ?");
    $success = $stmt->execute([$comment_id]);

    if ($success) {
        header("Location: admin.php?deleted=1");
        exit();
    } else {
        echo "Không thể xóa bình luận.";
    }
} else {
    header("Location: admin.php");
    exit();
}
?>
