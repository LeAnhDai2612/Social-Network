<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('USER_SESSION');
    session_start();
}
require_once '../core/config.php';

// Đảm bảo người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Lấy danh sách thông báo
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Đánh dấu là đã đọc
$conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0")
     ->execute([$_SESSION['user_id']]);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông báo</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .notification-item {
            padding: 12px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            transition: background-color 0.2s ease;
        }
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        .unread {
            background-color: #eef1ff;
            font-weight: bold;
        }
        .notification-link {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body class="container py-4">
    <h3 class="mb-4">🔔 Danh sách thông báo</h3>

    <?php if (empty($notifications)): ?>
        <div class="alert alert-info text-center">Bạn chưa có thông báo nào.</div>
    <?php else: ?>
        <div class="d-flex flex-column">
        <?php foreach ($notifications as $n): ?>
            <?php
            $link = "#";
            if ($n['type'] === 'like' && !empty($n['post_id'])) {
                $link = "index.php?post_id=" . $n['post_id'];
            } elseif ($n['type'] === 'reply' && !empty($n['post_id'])) {
                $link = "index.php?post_id=" . $n['post_id'] . "#comment-" . $n['comment_id'];
            }
            ?>
            <a href="<?= $link ?>" class="notification-link">
                <div class="notification-item <?= $n['is_read'] ? '' : 'unread' ?>">
                    <?= htmlspecialchars($n['content']) ?>
                    <div><small class="text-muted"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></small></div>
                </div>
            </a>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <script type="module" src="scripts/utility-functions.js"></script>
</body>
</html>
