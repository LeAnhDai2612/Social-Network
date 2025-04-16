<?php
session_name('USER_SESSION');

session_start();

require_once('../core/utility_functions.php');
redirect_if_not_logged_in();

require_once('post_display.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Social Network</title>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
        </script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/minisearch@6.1.0/dist/umd/index.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="css/style.css">
    
    <script type="module" src="scripts/show-search-suggestions.js" defer></script>
    <script src="scripts/lazy-load.js" defer></script>

    <script type="module" src="scripts/post-modal-handler.js" defer></script>
    <script type="module" src="scripts/post-more-options-handler.js" defer></script>
    <script type="module" src="scripts/post-likes-modal-handler.js" defer></script>
    <script type="module" src="scripts/post-interactions-handler.js" defer></script>
    <script type="module" src="scripts/follow-handler.js" defer></script>
</head>

<body class="h-100 w-100 m-0 p-0 preload">
    <?php include('partials/post_modal.php') ?>
    <?php include('partials/delete_post_modal.php') ?>
    <?php include('partials/post_likes_modal.php') ?>
    <?php include('partials/toast.php') ?>
    <div class="w-100 h-100 body-container container-fluid m-0 p-0">
        <?php include('partials/header.php'); ?>
        <?php include('partials/sidebar.php'); ?>
        <main class="page-home d-flex flex-column h-100 bg-light align-items-center justify-content-start">
            <div
                class="d-flex feed-container flex-column pt-5 pb-5 align-items-start align-items-center justify-content-center">
                <div class="feed-top w-100 mb-4">
                    <p class="h3 fw-semibold">Feed</p>
                </div>
                <div class="feed-posts-container d-flex flex-column align-items-center justify-content-center gap-4">
    <?php
    require_once('../core/db_functions.php');
    $pdo = connect_to_db();
    $current_user_id = $_SESSION['user_id'] ?? null;

    $post_id = $_GET['post_id'] ?? null;
    

    if ($post_id) {
        $stmt = $pdo->prepare("SELECT p.*, u.username, u.display_name, u.profile_picture_path 
                            FROM posts_table p 
                            JOIN users_table u ON p.user_id = u.id 
                            WHERE p.id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($post) {
          $poster_id = $post['user_id'] ?? null;
  
          if (
              is_user_blocked($pdo, $current_user_id, $poster_id) ||
              is_user_blocked($pdo, $poster_id, $current_user_id)
          ) {
              echo "<p class='text-muted'>Bạn không thể xem bài viết này.</p>";
          } else {
              display_posts($pdo, [$post]);
          }
        }
    } else {
      $posts = get_all_posts($pdo);

      // Lọc bỏ bài viết nếu người đăng hoặc mình bị chặn
      $filtered_posts = array_filter($posts, function($post) use ($pdo, $current_user_id) {
          $poster_id = $post['user_id'] ?? null;
          if (!$poster_id) return false;
      
          return !(
              is_user_blocked($pdo, $current_user_id, $poster_id) ||
              is_user_blocked($pdo, $poster_id, $current_user_id)
          );
      });
      
      display_posts($pdo, $filtered_posts);
      
    }
  
    ?>
</div>

            </div>
        </main>
        <?php include('partials/footer.php'); ?>
    </div>
    <div id="ai-chat-bubble" title="Chat với AI">
        <i class="bi bi-robot fs-4"></i>
    </div>

    <div id="ai-chat-window" class="card shadow-lg">
        <div id="ai-chat-header" class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Chat với AI</h6>
            <button type="button" id="ai-chat-close" class="btn-close small" aria-label="Đóng"></button>
        </div>
        <div id="ai-chat-messages" class="card-body overflow-auto">
            <!-- Tin nhắn sẽ được thêm vào đây bằng JavaScript -->
            <div class="ai-message">Xin chào! Bạn cần tôi giúp gì?</div>
        </div>
        <div id="ai-chat-input-area" class="card-footer">
            <form id="ai-chat-form" class="d-flex gap-2">
                <input type="text" id="ai-chat-input" class="form-control form-control-sm" placeholder="Nhập tin nhắn..." autocomplete="off">
                <button type="submit" id="ai-chat-send" class="btn btn-primary btn-sm">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>
    </div>
</body>
echo "<script src='scripts/comment-handler.js'></script>";

<script src="scripts/ai-chat-handler.js"></script>
<script type="module" src="scripts/utility-functions.js"></script>
</html>

<!-- Modal Báo Cáo Bootstrap -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-light text-dark darkmode-bg darkmode-text">
      <div class="modal-header">
        <h5 class="modal-title" id="reportModalLabel">Báo cáo bài viết</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <form id="reportForm">
        <div class="modal-body">
          <input type="hidden" name="post_id" id="report_post_id">

          <div class="mb-3">
            <label for="reason" class="form-label">Lý do:</label>
            <select name="reason" id="reason" class="form-select" required>
              <option value="">-- Chọn lý do --</option>
              <option value="spam">Spam</option>
              <option value="offensive">Nội dung phản cảm</option>
              <option value="other">Khác</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Mô tả thêm:</label>
            <textarea name="description" id="description" rows="4" class="form-control" placeholder="Chi tiết thêm nếu cần..."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-danger">Gửi báo cáo</button>
        </div>
      </form>
    </div>
  </div>

<div id="reportAlert" class="alert alert-success d-none fw-semibold mx-auto mt-3" role="alert" style="max-width: 500px;">
  ✅ Báo cáo của bạn đã được gửi đi.
</div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const reportForm = document.getElementById("reportForm");
  const reportModalEl = document.getElementById("reportModal");
  const reportModal = new bootstrap.Modal(reportModalEl);
  const alertBox = document.getElementById("reportAlert");

  document.querySelectorAll(".report-post-btn").forEach(btn => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      const postId = this.getAttribute("data-post-id");
      document.getElementById("report_post_id").value = postId;
      reportModal.show();
    });
  });

  reportForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(reportForm);

    fetch("../core/report_post.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        // ✅ Hiển thị alert bootstrap đẹp
        alertBox.classList.remove("d-none");
        alertBox.classList.add("show");

        // Ẩn sau 3 giây
        setTimeout(() => {
          alertBox.classList.add("d-none");
          alertBox.classList.remove("show");
        }, 3000);

        reportModal.hide();
        reportForm.reset();
      } else {
        alert("❌ " + data.message);
      }
    })
    .catch(err => {
      alert("❌ Gửi báo cáo thất bại!");
    });
  });
});
</script>