<?php
session_name('USER_SESSION');
session_start();
require_once '../core/db_functions.php';
require_once('../core/utility_functions.php');
redirect_if_not_logged_in();

$pdo = connect_to_db();
$blocked_users = get_blocked_users($pdo, $_SESSION['user_id']);

// Lấy giá trị từ session, thêm ?? '' để tránh lỗi nếu chưa có
$user_full_name = $_SESSION['user_full_name'] ?? '';
$user_username = $_SESSION['user_username'] ?? '';
$user_display_name = $_SESSION['user_display_name'] ?? '';
$user_bio = $_SESSION['user_bio'] ?? '';
$poster_profile_picture = $_SESSION['user_profile_picture_path'] ?? 'https://res.cloudinary.com/dy6o43c27/image/upload/v1743922855/socialnetwork/profile-pictures/hx5eeq7gbw70pjef0ugm.jpg';

$profile_pic_compression_settings = "w_100,h_100,c_fill,r_max/f_auto,q_auto:eco";
$profile_pic_transformed_url = add_transformation_parameters($poster_profile_picture, $profile_pic_compression_settings);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Chỉnh sửa hồ sơ</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous" defer>
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body class="bg-light">
    <div class="w-100 h-100 body-container container-fluid m-0 p-0">
        <?php include('partials/sidebar.php'); ?>
        <?php include('partials/header.php'); ?>

        <main class="page-settings container py-5">
            <div class="row justify-content-center g-4">

                <div class="col-lg-6 col-md-8">
                    <div class="card edit-profile-card h-100">
                        <div class="card-header fw-bold d-flex align-items-center bg-white">
                        <h5 class="m-0 p-0 fw-semibold text-nowrap text-body">Chỉnh sửa Trang cá nhân</h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                             <form id="edit-profile-form" class="flex-grow-1" autocomplete="off" novalidate="novalidate" method="POST"
                                enctype="multipart/form-data" action="execute_core_file.php?filename=process_edit_profile.php">

                                <div class="mb-3">
                                    <label class="form-label mb-2">Ảnh đại diện</label>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 flex-shrink-0">
                                            <img src="<?php echo htmlspecialchars($profile_pic_transformed_url); ?>"
                                                class="profile-picture-picker-image img-fluid rounded-circle"
                                                id="profile-picture-picker-image" alt="Ảnh đại diện hiện tại"
                                                style="width: 80px; height: 80px; object-fit: cover;" />
                                        </div>
                                        <div class="w-100">
                                            <label for="profile-picture-picker" class="btn btn-outline-primary w-100">
                                                <span id="profile-picture-label-text">Chọn ảnh mới</span>
                                            </label>
                                            <input type="file" name="profile_picture_picker" accept="image/png, image/jpeg, image/gif"
                                                class="form-control d-none" id="profile-picture-picker" autocomplete="off" />
                                            <div class="form-text">Chọn ảnh PNG, JPG hoặc GIF.</div>
                                            <div id="errors-container_custom-profile-picture"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="edit-profile-full-name" class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="edit-profile-full-name"
                                        placeholder="Họ và tên đầy đủ của bạn"
                                        value="<?php echo htmlspecialchars($user_full_name); ?>" name="full_name">
                                    <div id="errors-container_custom-full-name"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="edit-profile-display-name" class="form-label">Tên hiển thị</label>
                                    <input type="text" class="form-control" id="edit-profile-display-name"
                                        placeholder="Tên bạn muốn hiển thị"
                                        value="<?php echo htmlspecialchars($user_display_name); ?>" name="user_display_name" required>
                                    <div id="errors-container_custom-display-name"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="edit-profile-username" class="form-label">Tên người dùng (Username)</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="username-addon">@</span>
                                        <input type="text" class="form-control" id="edit-profile-username"
                                               placeholder="Tên đăng nhập duy nhất"
                                               value="<?php echo htmlspecialchars($user_username); ?>" name="username"
                                               aria-describedby="username-addon username-help" required>
                                    </div>
                                    <div id="username-help" class="form-text">
                                        Chỉ chứa chữ cái (a-z, A-Z), số (0-9) và dấu gạch dưới (_). Tên này phải là duy nhất.
                                    </div>
                                    <div id="errors-container_custom-username"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="bio" class="form-label">Giới thiệu</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="3"
                                        placeholder="Viết gì đó về bạn..."
                                        ><?php echo htmlspecialchars($user_bio); ?></textarea>
                                    <div id="errors-container_custom-bio"></div>
                                </div>

                                <div class="text-end mt-auto">
                                    <button type="submit" class="btn btn-primary fw-bold text-nowrap">Lưu thay đổi</button>
                                </div>

                                <div id="errors-container_custom-container"></div>
                            </form>
                            <div class="mt-3 text-center">
                                <a href="request_password_reset.php" class="link-secondary text-decoration-none small">Đặt lại mật khẩu?</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-8">
                    <div class="card h-100">
                         <div class="accordion" id="blockedUsersAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="blockedUsersHeader">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBlockedUsers" aria-expanded="false" aria-controls="collapseBlockedUsers">
                                        Danh sách người dùng đã chặn (<?= count($blocked_users) ?>)
                                    </button>
                                </h2>
                                <div id="collapseBlockedUsers" class="accordion-collapse collapse" aria-labelledby="blockedUsersHeader" data-bs-parent="#blockedUsersAccordion">
                                    <div class="accordion-body p-0">
                                        <?php if (empty($blocked_users)): ?>
                                            <p class="text-muted p-3 mb-0">Bạn chưa chặn người dùng nào.</p>
                                        <?php else: ?>
                                            <ul class="list-group list-group-flush">
                                                <?php foreach ($blocked_users as $user):
                                                    $blocked_user_avatar = $user['profile_picture_path'] ?? 'images/default_avatar.png';
                                                    $blocked_user_avatar_settings = "w_40,h_40,c_fill,r_max/f_auto,q_auto";
                                                    $blocked_user_avatar_url = add_transformation_parameters($blocked_user_avatar, $blocked_user_avatar_settings);
                                                ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                             <img src="<?= htmlspecialchars($blocked_user_avatar_url) ?>" alt="Avatar" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                             <span>
                                                                 <?= htmlspecialchars($user['display_name'] ?? $user['username']) ?>
                                                                 <small class="text-muted d-block">@<?= htmlspecialchars($user['username']) ?></small>
                                                             </span>
                                                        </div>
                                                        <form action="../core/block_user.php" method="POST" class="ms-2">
                                                            <input type="hidden" name="blocked_id" value="<?= $user['id'] ?>">
                                                            <input type="hidden" name="action" value="unblock">
                                                            <input type="hidden" name="redirect_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                                            <button class="btn btn-sm btn-outline-warning" type="submit" title="Bỏ chặn người dùng này">Bỏ chặn</button>
                                                        </form>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
        <?php include('partials/footer.php'); ?>
        <script type="module" src="scripts/utility-functions.js"></script>
    </div>

    <script>
        const profilePictureInput = document.getElementById("profile-picture-picker");
        const profilePictureLabel = document.getElementById("profile-picture-label-text");
        const profilePictureImage = document.getElementById("profile-picture-picker-image");

        if (profilePictureInput && profilePictureLabel && profilePictureImage) {
            profilePictureInput.addEventListener("change", (event) => {
                const files = event.target.files;
                if (files.length > 0) {
                    const fileName = files[0].name;
                    profilePictureLabel.textContent = fileName;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profilePictureImage.src = e.target.result;
                    }
                    reader.readAsDataURL(files[0]);
                } else {
                    profilePictureLabel.textContent = "Chọn ảnh mới";
                }
            });
        }
    </script>

</body>
</html>