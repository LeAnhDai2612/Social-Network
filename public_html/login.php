<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_name('USER_SESSION');

session_start();

$show_reset_form = false;
$show_otp_form = false;

if (isset($_SESSION['reset_password_verified']) && $_SESSION['reset_password_verified'] === true) {
    $show_reset_form = true;
} elseif (isset($_GET['reset_otp_sent'])) {
    $show_otp_form = true;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Log in · Social Network</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/just-validate@latest/dist/just-validate.production.min.js"></script>
    <script type="module" src="scripts/validate-login.js" defer></script>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<script type="module" src="scripts/utility-functions.js"></script>
<div>
    <?php include('partials/header.php'); ?>
    <main class="bg-light page-login d-flex flex-column w-100 align-items-center justify-content-center">
        <div class="container p-0 m-0">
            <div class="card login-card border">
                <div class="login-card-img-container row no-gutters">
                    <div class="col-md-6 p-0">
                        <img class="login-card-img"
                             src="https://res.cloudinary.com/dy6o43c27/image/upload/v1743922855/socialnetwork/profile-pictures/hx5eeq7gbw70pjef0ugm.jpg"
                             alt="">
                    </div>
                    <div class="col-md-6 p-0">
                        <div class="card-body h-100 d-flex flex-column justify-content-center p-0">

                            <!-- LOGIN FORM -->
                            <form id="login-form" class="d-flex flex-column w-100 p-5 gap-4 <?= $show_reset_form || $show_otp_form ? 'd-none' : '' ?>" method="POST"
                                  action="execute_core_file.php?filename=process_login.php">
                                <div>
                                    <p class="fw-bold fs-1 m-0">Xin chào!</p>
                                    <p class="fs-5 m-0">Nhập thông tin để đăng nhập</p>
                                </div>

                                <div class="form-floating">
                                    <input class="form-control bg-light px-3" id="username" name="username" type="text"
                                           placeholder="SĐT, Email, hoặc Tên đăng nhập" />
                                    <label for="username"><p class="m-0 px-3 bg-light">SĐT, Email hoặc Tên đăng nhập</p></label>
                                </div>

                                <div class="form-floating">
                                    <input class="form-control bg-light px-3" id="password" name="password" type="password"
                                           placeholder="Mật khẩu" />
                                    <label for="password"><p class="m-0 px-3 bg-light">Mật khẩu</p></label>
                                </div>

                                <button class="btn btn-primary fw-bold w-100" type="submit">Đăng nhập</button>

                               <?php if (isset($_GET['error'])): ?>
                                    <div style="color: red; margin-top: 10px; font-weight: bold;">
                                        <?= htmlspecialchars($_GET['error']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="text-center">
                                    <a href="#" id="forgot-password-toggle" class="link-primary text-decoration-none">Quên mật khẩu?</a>
                                </div>

                                <div id="login-error" class="alert alert-danger mb-0 d-none">Mật khẩu sai rồi, kiểm tra lại nhé</div>

                                <p class="m-0">
                                    <span class="text-muted">Bạn chưa có tài khoản?</span>
                                    <a href="register.php" class="link-underline link-underline-opacity-0 fw-semibold">Đăng ký</a>
                                </p>
                            </form>

                            <!-- FORGOT PASSWORD -->
                            <form id="forgot-password-form" class="d-none d-flex flex-column w-100 p-5 gap-4" method="POST"
                                  action="execute_core_file.php?filename=send_otp.php">
                                <p class="fw-bold fs-1 m-0">Quên mật khẩu?</p>
                                <p class="fs-5 m-0">Nhập Email hoặc SĐT để nhận mã OTP</p>

                                <div class="form-floating">
                                    <input class="form-control bg-light px-3" id="reset_email" name="reset_email" type="text"
                                           placeholder="Email hoặc Số điện thoại" required />
                                    <label for="reset_email"><p class="m-0 px-3 bg-light">Email hoặc SĐT</p></label>
                                </div>

                                <button class="btn btn-primary fw-bold w-100" type="submit">Gửi mã OTP</button>

                                <div class="text-center">
                                    <a href="#" id="back-to-login" class="link-primary text-decoration-none">Quay lại đăng nhập</a>
                                </div>
                            </form>
                            <!-- VERIFY OTP FORM -->
                            <form id="verify-otp-form" class="d-none d-flex flex-column w-100 p-5 gap-4" method="POST"
                                action="execute_core_file.php?filename=verify_otp.php">
                                <p class="fw-bold fs-1 m-0">Nhập mã xác thực</p>
                                <p class="fs-5 m-0">Chúng tôi đã gửi mã OTP đến email của bạn</p>

                                <input type="hidden" name="email" value="<?= $_SESSION['reset_target_email'] ?? '' ?>">

                                <div class="form-floating">
                                    <input class="form-control bg-light px-3" id="otp_code" name="otp_code" type="text"
                                        placeholder="Nhập mã OTP" maxlength="6" required />
                                    <label for="otp_code"><p class="m-0 px-3 bg-light">Mã OTP</p></label>
                                </div>

                                <button class="btn btn-primary fw-bold w-100" type="submit">Xác thực OTP</button>

                                <div class="text-center">
                                    <a href="#" id="back-to-forgot" class="link-primary text-decoration-none">Quay lại nhập email</a>
                                </div>
                            </form>


                            <!-- RESET PASSWORD -->
                            <form id="reset-password-form" class="d-flex flex-column w-100 p-5 gap-4 <?= $show_reset_form ? '' : 'd-none' ?>" method="POST"
                                  action="execute_core_file.php?filename=reset_password.php">
                                <p class="fw-bold fs-1 m-0">Đặt lại mật khẩu</p>
                                <p class="fs-5 m-0">Nhập mật khẩu mới của bạn</p>

                                <div class="form-floating">
                                    <input class="form-control bg-light px-3" id="new_password" name="new_password" type="password"
                                           placeholder="Mật khẩu mới" required />
                                    <label for="new_password"><p class="m-0 px-3 bg-light">Mật khẩu mới</p></label>
                                </div>

                                <div class="form-floating">
                                    <input class="form-control bg-light px-3" id="confirm_password" name="confirm_password" type="password"
                                           placeholder="Xác nhận mật khẩu" required />
                                    <label for="confirm_password"><p class="m-0 px-3 bg-light">Xác nhận mật khẩu</p></label>
                                </div>

                                <button class="btn btn-success fw-bold w-100" type="submit">Đặt lại mật khẩu</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include('partials/footer.php'); ?>
</div>

<script>
    const loginForm = document.getElementById('login-form');
    const forgotForm = document.getElementById('forgot-password-form');
    const otpForm = document.getElementById('verify-otp-form');
    const resetForm = document.getElementById('reset-password-form');

    // Hiện form Xác minh OTP nếu vừa gửi thành công
    <?php if (isset($_GET['reset_otp_sent'])): ?>
        loginForm.classList.add('d-none');
        forgotForm.classList.add('d-none');
        otpForm.classList.remove('d-none');
    <?php endif; ?>

    // Hiện form Đặt lại mật khẩu nếu xác minh xong OTP
    <?php if (isset($_GET['show_reset_password']) && ($_SESSION['reset_password_verified'] ?? false)): ?>
        loginForm.classList.add('d-none');
        forgotForm.classList.add('d-none');
        otpForm.classList.add('d-none');
        resetForm.classList.remove('d-none');
    <?php endif; ?>

    // Các nút chuyển đổi form
    document.getElementById('forgot-password-toggle').addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.classList.add('d-none');
        forgotForm.classList.remove('d-none');
    });

    document.getElementById('back-to-login').addEventListener('click', (e) => {
        e.preventDefault();
        forgotForm.classList.add('d-none');
        otpForm.classList.add('d-none');
        resetForm.classList.add('d-none');
        loginForm.classList.remove('d-none');
    });

    document.getElementById('back-to-forgot')?.addEventListener('click', (e) => {
        e.preventDefault();
        otpForm.classList.add('d-none');
        forgotForm.classList.remove('d-none');
    });
</script>
