<?php
session_name('USER_SESSION');

session_start();
require_once '../core/utility_functions.php';

// Kiểm tra session hợp lệ
if (
    empty($_SERVER['HTTP_REFERER']) ||
    !isset($_SESSION['registration_complete']) ||
    !$_SESSION['registration_complete'] ||
    !isset($_SESSION['user_full_name'])
) {
    $base_url = get_base_url();
    header('Location: ' . $base_url . 'register.php');
    exit();
}

$display_name = $_SESSION['user_full_name'];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hoàn tất hồ sơ · Social Network</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>
    <script type="module" src="scripts/validate-profile-setup.js" defer></script>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div>
        <?php include('partials/header.php'); ?>

        <main class="page-login d-flex flex-column w-100 h-100 align-items-center justify-content-center">
            <form id="setup-profile-form" autocomplete="off" novalidate class="bg-white w-100"
                method="POST" enctype="multipart/form-data"
                action="execute_core_file.php?filename=process_profile_setup.php">
                <div class="card edit-profile-card w-100">
                    <div class="card-header fw-bold d-flex align-items-center">
                        <div class="d-flex align-items-center w-100">
                            <h5 class="text-center m-0 p-0 text-nowrap">Hoàn tất</h5>
                        </div>
                    </div>
                    <div class="card-body">

                        <!-- Ảnh đại diện -->
                        <div class="mb-3">
                            <label for="display-name" class="form-label">Ảnh đại diện</label>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <img src="https://res.cloudinary.com/dy6o43c27/image/upload/v1743922855/socialnetwork/profile-pictures/hx5eeq7gbw70pjef0ugm.jpg"
                                         class="profile-picture-picker-image img-fluid rounded-circle"
                                         id="profile-picture-picker-image" alt="profile picture" />
                                </div>
                                <div class="upload-btn btn btn-rounded p-0">
                                    <label class="choose-profile-picture-label form-label mb-0 w-100 h-100 p-2"
                                        for="profile-picture-picker">Tải ảnh</label>
                                    <input type="file" name="profile_picture_picker" accept="image/*"
                                        class="form-control d-none" id="profile-picture-picker" autocomplete="off" />
                                </div>
                            </div>
                            <div id="errors-container_custom-profile-picture"></div>
                        </div>

                        <!-- Tên hiển thị -->
                        <div class="mb-3">
                            <label for="edit-profile-display-name" class="form-label">Tên hiển thị</label>
                            <input type="text" class="form-control" id="edit-profile-display-name"
                                placeholder="<?= htmlspecialchars($display_name) ?>"
                                value="<?= htmlspecialchars($display_name) ?>" name="user_display_name">
                            <div id="errors-container_custom-display-name"></div>
                        </div>

                        <!-- Bio -->
                        <div class="mb-4">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="profile-bio-textarea form-control" id="bio" name="bio" rows="3"
                                placeholder="Xin chào <?= htmlspecialchars($display_name) ?>!">Xin chào <?= htmlspecialchars($display_name) ?>!</textarea>
                            <div id="errors-container_custom-bio"></div>
                        </div>

                        <!-- Nút xác nhận -->
                        <div class="d-flex flex-column align-items-center">
                            <button type="submit" name="submit" class="btn btn-primary fw-bold w-100 mb-1">Xác nhận</button>
                            <a class="col btn btn-link text-center link-underline link-underline-opacity-0 fw-semibold"
                               href="register.php">Quay về</a>
                        </div>
                        
                    </div>
                </div>
                <div id="errors-container_custom-container"></div>
            </form>
        </main>

        <?php include('partials/footer.php'); ?>
    </div>
</body>

</html>
<script>
  const input = document.getElementById("profile-picture-picker");
  const label = document.querySelector(".choose-profile-picture-label");

  input.addEventListener("change", () => {
    const fileName = input.files.length > 0 ? input.files[0].name : "Tải ảnh";
    label.innerText = fileName;
  });
</script>
