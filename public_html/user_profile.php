<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_name('USER_SESSION');
session_start();

require_once('../core/utility_functions.php');
redirect_if_not_logged_in();

require_once('../core/db_functions.php');
require_once('post_display.php');

$conn = connect_to_db();
$pdo = $conn;

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];


if (isset($_GET['user_id']) && !empty($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $current_user_id_from_get = filter_var($_GET['user_id'], FILTER_VALIDATE_INT);

    if ($current_user_id_from_get === false) {
         echo "Invalid User ID.";
         include('partials/footer.php');
         exit;
    }
    $current_user_id = $current_user_id_from_get;

    $is_logged_in_user_profile = ($current_user_id === $user_id);

    $is_user_blocked = false;
    $is_blocked_by_user = false;
    if (!$is_logged_in_user_profile) {
        $is_user_blocked = is_user_blocked($pdo, $user_id, $current_user_id);
        $is_blocked_by_user = is_user_blocked($pdo, $current_user_id, $user_id);

        if ($is_user_blocked) {
            echo "<div class='text-center mt-5'>
                <p class='fs-5 text-muted'>Bạn đã chặn người dùng này.</p>
                <p class='text-muted'>Hãy vào phần <strong>Cài đặt > Danh sách chặn</strong> để bỏ chặn nếu muốn xem lại hồ sơ.</p>
            </div>";
            include('partials/footer.php'); // Include footer before exiting
            exit;
        }

        if ($is_blocked_by_user) {
            echo "<div class='text-center mt-5'>
                <p class='fs-5 text-muted'>Bạn không thể xem hồ sơ này.</p>
            </div>";
            include('partials/footer.php'); // Include footer before exiting
            exit;
        }
    }


    $user_info = get_user_info($pdo, $current_user_id);

    if (!$user_info) {
        echo "<div class='text-center mt-5'><p class='fs-5 text-muted'>Không tìm thấy người dùng.</p></div>";
        include('partials/footer.php'); // Include footer before exiting
        exit;
    }

    $user_bio = isset($user_info['bio']) ? nl2br(htmlspecialchars($user_info['bio'])) : '';

    $poster_profile_picture_url = $user_info['profile_picture_path'] ?? 'https://res.cloudinary.com/dy6o43c27/image/upload/v1743922855/socialnetwork/profile-pictures/hx5eeq7gbw70pjef0ugm.jpg';
    $poster_profile_pic_compression_settings = "w_400/f_auto,q_auto:eco";
    $poster_profile_pic_transformed_url = add_transformation_parameters($poster_profile_picture_url, $poster_profile_pic_compression_settings);

    $user_posts_amount = get_user_post_count($pdo, $current_user_id);
    $user_followers = get_user_followers($pdo, $current_user_id); // You might not need the full list here if only displaying count
    $user_followers_amount = count($user_followers); // Consider a dedicated count function if performance matters
    $user_following = get_followed_users_by_user($pdo, $current_user_id); // Same consideration as followers
    $user_following_amount = count($user_following);

    $is_followed_by_user = false;
    if (!$is_logged_in_user_profile) {
         $is_followed_by_user = does_row_exist($pdo, 'followers_table', 'follower_id', $user_id, 'followed_id', $current_user_id);
    }
    $follow_button_checked_attribute = $is_followed_by_user ? '' : 'checked';

} else {
    // Ensure basic HTML structure is present for error messages too
    echo "<!DOCTYPE html><html><head><title>Lỗi</title>";
    // Include necessary CSS for layout
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '<link rel="stylesheet" href="css/style.css">';
    echo "</head><body class='h-100 w-100 m-0 p-0'>";
    echo "<div class='d-flex justify-content-center align-items-center vh-100'><div class='text-center mt-5'><p class='fs-5 text-muted'>ID người dùng không hợp lệ.</p></div></div>";
    include('partials/footer.php'); // Include footer before exiting
    echo "</body></html>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($user_info['display_name'] ?? 'User Profile'); ?> - Social Network</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous" defer>
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts/lazy-load.js" defer></script>
    <script type="module" src="scripts/post-modal-handler.js" defer></script>
    <script type="module" src="scripts/post-more-options-handler.js" defer></script>
    <script type="module" src="scripts/post-likes-modal-handler.js" defer></script>
    <script type="module" src="scripts/post-interactions-handler.js" defer></script>
    <script type="module" src="scripts/follow-handler.js" defer></script>
</head>
<body class="h-100 w-100 m-0 p-0 preload">
    <?php include('partials/delete_post_modal.php') ?>
    <?php include('partials/post_likes_modal.php') ?>
    <?php include('partials/toast.php') ?>
    <div class="w-100 h-100 body-container container-fluid m-0 p-0">
        <?php include('partials/sidebar.php'); ?>
        <?php include('partials/header.php'); ?>
        <main class="page-user-profile bg-light">
            <div class="py-5 d-flex flex-column h-100 align-items-center gap-5">
                <div class="profile-info d-flex pb-0 gap-4 align-items-center justify-content-start mb-3">
                    <img class="user-profile-profile-picture flex-shrink-0" src="<?php echo htmlspecialchars($poster_profile_pic_transformed_url); ?>" alt="<?php echo htmlspecialchars($user_info['display_name'] ?? ''); ?>'s profile picture">
                    <div class="user-profile-text-info d-flex flex-column gap-3 w-100 p-1">
                        <div class="d-flex gap-4 align-items-center">
                            <div>
                                <p class="user-profile-display-name fs-5 fw-bold text-body m-0">
                                    <?php echo htmlspecialchars($user_info['display_name'] ?? 'N/A'); ?>
                                </p>
                                <p class="user-profile-username text-secondary fs-6 m-0">
                                    <?php echo '@' . htmlspecialchars($user_info['username'] ?? 'N/A'); ?>
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <?php if ($is_logged_in_user_profile): ?>
                                    <a href="edit_profile.php" class="btn btn-outline-secondary" role="button">Chỉnh sửa</a>
                                <?php else: ?>
                                    <input type="checkbox" class="btn-check" id="user-profile-follow-button" autocomplete="off" <?php echo $follow_button_checked_attribute; ?>>
                                    <label class="btn btn-primary follow-toggle-btn" for="user-profile-follow-button" data-user-id="<?php echo $current_user_id; ?>">
                                        <span class="follow-text fw-medium">Theo dõi</span>
                                        <span class="unfollow-text">Hủy theo dõi</span>
                                    </label>
                                    <button class="btn btn-outline-danger" id="blockBtn" data-user-id="<?php echo $current_user_id; ?>" data-username="<?php echo htmlspecialchars($user_info['username'] ?? ''); ?>">Chặn</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="d-flex gap-3">
                            <a id="user-profile-posts-amount" class='d-flex align-items-center gap-1 text-decoration-none text-body fw-semibold' href="#user-profile-posts" style="cursor: pointer;">
                                <p class="m-0 fs-6"><?= htmlspecialchars($user_posts_amount) ?></p>
                                <p class="m-0 fs-6"><?= $user_posts_amount === 1 ? 'Bài đăng' : 'Bài viết' ?></p>
                            </a>
                            <a id="user-profile-followers-amount" class='d-flex align-items-center gap-1 text-decoration-none text-body fw-semibold' style="cursor: pointer;">
                                <p class="m-0 fs-6"><?= htmlspecialchars($user_followers_amount) ?></p>
                                <p class="m-0 fs-6"><?= $user_followers_amount === 1 ? 'Follower' : 'Followers' ?></p>
                            </a>
                            <a id="user-profile-following-amount" class='d-flex align-items-center gap-1 text-decoration-none text-body fw-semibold' style="cursor: pointer;">
                                <p class="m-0 fs-6"><?= htmlspecialchars($user_following_amount) ?></p>
                                <p class="m-0 fs-6">Following</p>
                            </a>
                        </div>
                         <?php if (!empty($user_bio)): ?>
                            <div class="user-profile-bio-container">
                                <p class="fw-semibold m-0 fs-6">Bio</p>
                                <p class="user-profile-bio m-0">
                                    <?php echo $user_bio; ?>
                                </p>
                            </div>
                         <?php endif; ?>
                    </div>
                </div>
                <div class="d-flex feed-container flex-column align-items-start align-items-center justify-content-center">
                    <div class="feed-top w-100 mb-4">
                        <h4 id="user-profile-posts" class="fw-semibold text-body">Bài viết</h4>
                    </div>
                    <div class="feed-posts-container p-0 d-flex flex-column align-items-center justify-content-center w-100 gap-4">
                        <?php
                            if ($user_posts_amount > 0) {
                                display_user_posts($current_user_id);
                            } else {
                                echo "<div class='text-center d-flex flex-column align-items-center gap-1 mt-4'>
                                    <i class='text-secondary bi bi-camera h1'></i>
                                    <p class='text-secondary fs-5'>Chưa có bài viết nào.</p>
                                </div>";
                            }
                        ?>
                    </div>
                </div>

                 <!-- Modal danh sách followers/following -->
                 <div class="modal fade" id="followListModal" tabindex="-1" aria-labelledby="followListModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="followListModalLabel">Danh sách</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body" id="follow-list-content">
                            <div class="text-center"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div> Đang tải...</div>
                        </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal -->

            </div>
        </main>
        <?php include('partials/footer.php'); ?>
    </div>

    <!-- Modal Xác Nhận Chặn Người Dùng -->
    <div class="modal fade" id="blockConfirmModal" tabindex="-1" aria-labelledby="blockConfirmModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="blockConfirmModalLabel">Xác nhận chặn</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>
          <div class="modal-body">
            Bạn có chắc chắn muốn chặn người dùng <strong id="block-confirm-username"></strong> không?
            <p class="text-muted small mt-2">Bạn sẽ không thể xem hồ sơ hoặc bài viết của họ và họ cũng không thể xem của bạn.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="button" class="btn btn-danger" id="confirmBlockActionBtn">Chặn</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Hết Modal Xác Nhận Chặn -->

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- Khởi tạo Modal Xác nhận Chặn ---
            const blockConfirmModalElement = document.getElementById('blockConfirmModal');
            const blockConfirmModal = blockConfirmModalElement ? bootstrap.Modal.getOrCreateInstance(blockConfirmModalElement) : null;
            const blockConfirmUsernameElement = document.getElementById('block-confirm-username');
            const confirmBlockActionBtn = document.getElementById('confirmBlockActionBtn');

            // --- Biến để lưu trữ thông tin người dùng cần chặn tạm thời ---
            let userIdToBlockGlobally = null;

            // --- Xử lý nút Chặn ban đầu ---
            const blockBtn = document.getElementById('blockBtn');
            if (blockBtn && blockConfirmModal) { // Chỉ thêm listener nếu nút và modal tồn tại
                blockBtn.addEventListener('click', function() {
                    const userIdToBlock = this.dataset.userId;
                    const usernameToBlock = this.dataset.username;
                    userIdToBlockGlobally = userIdToBlock;

                    if (blockConfirmUsernameElement) {
                        blockConfirmUsernameElement.textContent = `@${usernameToBlock}`;
                    }
                    blockConfirmModal.show(); // Hiển thị modal xác nhận
                });
            }

            // --- Xử lý nút xác nhận Chặn trong Modal ---
            if (confirmBlockActionBtn && blockConfirmModal) { // Chỉ thêm listener nếu nút và modal tồn tại
                confirmBlockActionBtn.addEventListener('click', function() {
                    if (!userIdToBlockGlobally) {
                        console.error("Không tìm thấy user ID để chặn.");
                        blockConfirmModal.hide();
                        return;
                    }

                    fetch('../core/block_user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new URLSearchParams({
                            'blocked_id': userIdToBlockGlobally,
                            'action': 'block'
                        })
                    })
                    .then(response => {
                        // Kiểm tra lỗi HTTP trước khi cố gắng parse JSON
                        if (!response.ok) {
                             return response.text().then(text => { // Đọc lỗi dạng text trước
                                try {
                                    const errData = JSON.parse(text); // Thử parse JSON
                                    throw new Error(errData.message || `Lỗi máy chủ: ${response.status}`);
                                } catch (e) { // Nếu không phải JSON
                                    throw new Error(text || `Lỗi máy chủ: ${response.status}`); // Ném lỗi dạng text
                                }
                             });
                        }
                        return response.json(); // Nếu response ok, parse JSON
                    })
                    .then(data => {
                        if (data.success) {
                            if (typeof showToast === 'function') {
                                showToast(data.message || 'Đã chặn người dùng thành công!', 'success');
                            } else {
                                alert(data.message || 'Đã chặn người dùng thành công!');
                            }
                            setTimeout(() => {
                                window.location.href = 'index.php';
                            }, 1500);
                        } else {
                            // Hiển thị lỗi từ server nếu có
                            if (typeof showToast === 'function') {
                                showToast(data.message || 'Không thể chặn người dùng.', 'error');
                            } else {
                                alert(data.message || 'Không thể chặn người dùng.');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error blocking user:', error);
                         if (typeof showToast === 'function') {
                            showToast(`Lỗi: ${error.message}`, 'error');
                        } else {
                            alert(`Đã xảy ra lỗi khi chặn người dùng: ${error.message}`);
                        }
                    })
                    .finally(() => {
                         blockConfirmModal.hide(); // Luôn ẩn modal
                         userIdToBlockGlobally = null; // Reset ID
                    });
                });
            }

            // --- Xử lý Modal Followers/Following (Giữ nguyên) ---
            const followersLink = document.getElementById('user-profile-followers-amount');
            const followListModalElement = document.getElementById('followListModal');
            // Kiểm tra sự tồn tại của modal trước khi khởi tạo
            const followListModal = followListModalElement ? bootstrap.Modal.getOrCreateInstance(followListModalElement) : null;
            const followListModalLabel = document.getElementById('followListModalLabel');
            const followListContent = document.getElementById('follow-list-content');
            const loadingHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div> Đang tải...</div>';

            if (followersLink && followListModal && followListModalLabel && followListContent) { // Thêm kiểm tra cho các phần tử modal
                followersLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    followListModalLabel.textContent = "Người theo dõi";
                    followListContent.innerHTML = loadingHTML;
                    followListModal.show();

                    fetch(`../core/get_user_followers.php?user_id=<?= $current_user_id ?>`)
                      .then(response => {
                          if (!response.ok) throw new Error('Network response was not ok.');
                          return response.json();
                      })
                      .then(data => {
                          followListContent.innerHTML = "";
                          if (data?.success && data.followers.length > 0) {
                              data.followers.forEach(user => {
                                  const profilePic = user.profile_picture_path ? encodeURI(user.profile_picture_path) : 'https://res.cloudinary.com/dy6o43c27/image/upload/v1743922855/socialnetwork/profile-pictures/hx5eeq7gbw70pjef0ugm.jpg';
                                  const displayName = user.display_name ? user.display_name.replace(/</g, "<").replace(/>/g, ">") : 'N/A';
                                  const username = user.username ? user.username.replace(/</g, "<").replace(/>/g, ">") : 'N/A';
                                  const userId = user.id;
                                  // Kiểm tra userId có hợp lệ không trước khi tạo link
                                  if(userId) {
                                    followListContent.innerHTML += `
                                        <a href="user_profile.php?user_id=${userId}" class="d-flex gap-3 align-items-center border-bottom py-2 text-decoration-none text-dark user-follow-entry">
                                            <img src="${profilePic}" style="width:40px;height:40px;border-radius:50%; object-fit: cover;" alt="${displayName}'s profile picture">
                                            <div>
                                                <div class="fw-semibold">${displayName}</div>
                                                <div class="text-muted small">@${username}</div>
                                            </div>
                                        </a>`;
                                  }
                              });
                          } else if (data?.success) {
                              followListContent.innerHTML = "<p class='text-muted text-center my-3'>Không có người theo dõi nào.</p>";
                          } else {
                               followListContent.innerHTML = `<p class='text-danger text-center my-3'>${data?.message || 'Không thể tải danh sách.'}</p>`;
                          }
                      })
                      .catch(error => {
                          console.error('Error fetching followers:', error);
                          followListContent.innerHTML = "<p class='text-danger text-center my-3'>Đã xảy ra lỗi khi tải danh sách người theo dõi.</p>";
                      });
                });
            }

            const followingLink = document.getElementById('user-profile-following-amount');
             if (followingLink && followListModal && followListModalLabel && followListContent) { // Thêm kiểm tra
                followingLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    followListModalLabel.textContent = "Đang theo dõi";
                    followListContent.innerHTML = loadingHTML;
                    followListModal.show();

                    fetch(`../core/get_user_following.php?user_id=<?= $current_user_id ?>`)
                      .then(response => {
                           if (!response.ok) throw new Error('Network response was not ok.');
                           return response.json();
                      })
                      .then(data => {
                          followListContent.innerHTML = "";
                          if (data?.success && data.following.length > 0) {
                              data.following.forEach(user => {
                                  const profilePic = user.profile_picture_path ? encodeURI(user.profile_picture_path) : 'https://res.cloudinary.com/dy6o43c27/image/upload/v1743922855/socialnetwork/profile-pictures/hx5eeq7gbw70pjef0ugm.jpg';
                                  const displayName = user.display_name ? user.display_name.replace(/</g, "<").replace(/>/g, ">") : 'N/A';
                                  const username = user.username ? user.username.replace(/</g, "<").replace(/>/g, ">") : 'N/A';
                                  const userId = user.id;
                                  if(userId) {
                                    followListContent.innerHTML += `
                                        <a href="user_profile.php?user_id=${userId}" class="d-flex gap-3 align-items-center border-bottom py-2 text-decoration-none text-dark user-follow-entry">
                                            <img src="${profilePic}" style="width:40px;height:40px;border-radius:50%; object-fit: cover;" alt="${displayName}'s profile picture">
                                            <div>
                                                <div class="fw-semibold">${displayName}</div>
                                                <div class="text-muted small">@${username}</div>
                                            </div>
                                        </a>`;
                                    }
                              });
                          } else if (data?.success) {
                              followListContent.innerHTML = "<p class='text-muted text-center my-3'>Chưa theo dõi người dùng nào.</p>";
                          } else {
                              followListContent.innerHTML = `<p class='text-danger text-center my-3'>${data?.message || 'Không thể tải danh sách.'}</p>`;
                          }
                      })
                      .catch(error => {
                          console.error('Error fetching following list:', error);
                           followListContent.innerHTML = "<p class='text-danger text-center my-3'>Đã xảy ra lỗi khi tải danh sách đang theo dõi.</p>";
                      });
                });
            }

            // --- Xử lý link Posts (Giữ nguyên) ---
            const postsLink = document.getElementById('user-profile-posts-amount');
            const postsSection = document.getElementById('user-profile-posts');
            if (postsLink && postsSection) {
                postsLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    postsSection.scrollIntoView({ behavior: 'smooth' });
                });
            }

        });
    </script>
     <script src="scripts/comment-handler.js"></script>
     <script type="module" src="scripts/utility-functions.js"></script>
</body>
</html>