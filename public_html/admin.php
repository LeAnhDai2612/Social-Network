<?php
$page = $_GET['page'] ?? 'dashboard';
require_once '../core/admin_only.php';
require_once '../core/db_functions.php';
$conn = connect_to_db();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Trang quản trị hệ thống</title>
  <link rel="icon" type="image/x-icon" href="images/favicon.ico">
  <style>
    body { font-family: Arial, sans-serif; margin: 0; }
    nav { background-color: #333; padding: 10px; }
    nav a {
      color: white; text-decoration: none; margin-right: 15px;
      font-weight: bold; padding: 6px 10px; border-radius: 5px;
    }
    nav a.active { background-color: #555; }
    .container { padding: 20px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; vertical-align: top; }
    th { background-color: #f0f0f0; }
    .button, .delete-button, .unban, .ban, .view-post, .delete-btn {
      padding: 6px 10px;
      border-radius: 4px;
      color: white;
      font-size: 14px;
      text-decoration: none;
    }
    .ban { background-color: #e74c3c; }
    .unban { background-color: #2ecc71; }
    .delete-button { background-color: #e74c3c; }
    .view-post { background-color: #3498db; }
    .delete-btn { background-color: #e74c3c; font-size: 13px; }
  </style>
</head>
<body>
<h2>Trang quản trị hệ thống</h2>
<nav>
  <a href="?page=dashboard" class="<?= $page === 'dashboard' ? 'active' : '' ?>">👥 Người dùng</a>
  <a href="?page=posts" class="<?= $page === 'posts' ? 'active' : '' ?>">📝 Bài viết</a>
  <a href="?page=comments" class="<?= $page === 'comments' ? 'active' : '' ?>">💬 Bình luận</a>
  <a href="?page=reports" class="<?= $page === 'reports' ? 'active' : '' ?>">📄 Báo cáo</a>
</nav>
<div class="container">

<?php if ($page === 'dashboard'): ?>
<h2>🛡️ Quản trị người dùng</h2>
<?php
$stmt = $conn->prepare("SELECT id, username, full_name, email, role, is_banned FROM users_table ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table>
  <tr>
    <th>ID</th><th>Username</th><th>Họ tên</th><th>Email</th><th>Role</th><th>Trạng thái</th><th>Thao tác</th>
  </tr>
  <?php foreach ($users as $user): ?>
  <tr>
    <td><?= htmlspecialchars($user['id']) ?></td>
    <td><?= htmlspecialchars($user['username']) ?></td>
    <td><?= htmlspecialchars($user['full_name']) ?></td>
    <td><?= htmlspecialchars($user['email']) ?></td>
    <td><strong><?= strtoupper($user['role']) ?></strong></td>
    <td style="color: <?= $user['is_banned'] ? 'red' : 'green' ?>">
      <?= $user['is_banned'] ? 'Đã bị cấm' : 'Bình thường' ?>
    </td>
    <td>
      <?php if ($user['id'] !== $_SESSION['user_id']): ?>
        <a class="button <?= $user['is_banned'] ? 'unban' : 'ban' ?>" href="toggle_ban.php?id=<?= $user['id'] ?>&action=<?= $user['is_banned'] ? 'unban' : 'ban' ?>">
          <?= $user['is_banned'] ? 'Mở cấm' : 'Cấm' ?>
        </a>
      <?php else: ?>
        <em>Chính bạn</em>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<?php if ($page === 'posts'): ?>
<h2>📝 Quản lý bài viết người dùng</h2>
<?php
$sql = "SELECT p.id, p.caption, p.media_path, p.media_type, p.created_at, u.username, u.full_name
        FROM posts_table p
        JOIN users_table u ON p.user_id = u.id
        ORDER BY p.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table>
  <tr>
    <th>ID</th><th>Người đăng</th><th>Nội dung</th><th>Media</th><th>Thời gian</th><th>Thao tác</th>
  </tr>
  <?php foreach ($posts as $post): ?>
  <tr>
    <td><?= $post['id'] ?></td>
    <td><?= htmlspecialchars($post['full_name']) ?> (<?= $post['username'] ?>)</td>
    <td><?= nl2br(htmlspecialchars($post['caption'])) ?></td>
    <td>
      <?php if (!empty($post['media_path'])): ?>
        <?php if ($post['media_type'] === 'image'): ?>
          <img src="<?= htmlspecialchars($post['media_path']) ?>" alt="Ảnh" style="max-width:200px;max-height:200px;">
        <?php elseif ($post['media_type'] === 'video'): ?>
          <video controls style="max-width:200px;max-height:200px;">
            <source src="<?= htmlspecialchars($post['media_path']) ?>" type="video/mp4">
            Trình duyệt không hỗ trợ video.
          </video>
        <?php else: ?>Không rõ loại media<?php endif; ?>
      <?php else: ?>Không có media<?php endif; ?>
    </td>
    <td><?= $post['created_at'] ?></td>
    <td><a class="delete-button" href="delete_post_by_admin.php?id=<?= $post['id'] ?>" onclick="return confirm('Xác nhận xoá bài viết này?')">Xoá</a></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<?php if ($page === 'comments'): ?>
<h2>💬 Danh sách bình luận</h2>
<?php
$query = "SELECT c.id, c.content, c.created_at, 
                 u.username AS commenter_username,
                 p.id AS post_id, p.caption
          FROM comments_table c
          JOIN users_table u ON c.user_id = u.id
          JOIN posts_table p ON c.post_id = p.id
          ORDER BY c.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table>
  <tr>
    <th>Người bình luận</th><th>Nội dung</th><th>Bài viết</th><th>Thời gian</th><th>Tác vụ</th>
  </tr>
  <?php foreach ($comments as $c): ?>
  <tr>
    <td><?= htmlspecialchars($c['commenter_username']) ?></td>
    <td><?= nl2br(htmlspecialchars($c['content'])) ?></td>
    <td><a class="post-link" href="index.php?post_id=<?= $c['post_id'] ?>#comment-<?= $c['id'] ?>" target="_blank">Xem bài viết</a></td>
    <td><?= date("d/m/Y H:i", strtotime($c['created_at'])) ?></td>
    <td>
      <form method="POST" action="delete_comment_admin.php" onsubmit="return confirm('Xóa bình luận này?');">
        <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
        <button type="submit" class="delete-btn">Xóa</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<?php if ($page === 'reports'): ?>
<h2>📄 Danh sách báo cáo bài viết</h2>
<?php
$query = "SELECT r.id, r.reason, r.description, r.report_time, 
                 u.username AS reporter_username,
                 p.id AS post_id, p.caption
          FROM reports_table r
          JOIN users_table u ON r.reporter_id = u.id
          JOIN posts_table p ON r.post_id = p.id
          ORDER BY r.report_time DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table>
  <tr>
    <th>Người báo cáo</th><th>Lý do</th><th>Mô tả</th><th>Thời gian</th><th>Xem bài viết</th><th>Thao tác</th>
  </tr>
  <?php foreach ($reports as $report): ?>
  <tr>
    <td><?= htmlspecialchars($report['reporter_username']) ?></td>
    <td><?= htmlspecialchars($report['reason']) ?></td>
    <td><?= nl2br(htmlspecialchars($report['description'])) ?></td>
    <td><?= date("d/m/Y H:i", strtotime($report['report_time'])) ?></td>
    <td><a class="view-post" href="index.php?post_id=<?= $report['post_id'] ?>" target="_blank">Xem bài viết</a></td>
    <td><a class="delete-button" href="delete_post_by_admin.php?id=<?= $report['post_id'] ?>" onclick="return confirm('Xác nhận xoá bài viết này?')">Xoá</a></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

</div>
<!-- Logout Link -->
<div class="w-100 navbar-nav mt-auto">
  <a class="nav-link d-flex px-2 ms-5 w-100" href="logout.php">
    <i class="nav-link-icon bi bi-box-arrow-right me-4 d-flex align-items-center justify-content-center"></i>
    Đăng xuất
  </a>
</div>
</body>
</html>