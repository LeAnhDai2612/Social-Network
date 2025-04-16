<?php
session_name('USER_SESSION');

session_start();
$email = $_SESSION['pending_user']['email'] ?? '';

if (!$email) {
    header("Location: register.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xác thực OTP · Social Network</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div>
        <?php include('partials/header.php'); ?>

        <main class="bg-light page-register d-flex flex-column w-100 align-items-center justify-content-center">
            <div class="container p-0 m-0">
                <div class="card register-card border">
                    <div class="register-card-img-container row no-gutters">
                        <div class="col-md-6 p-0">
                            <img class="register-card-img h-100 w-100"
                                src="https://res.cloudinary.com/dy6o43c27/image/upload/v1743795611/momento/profile-pictures/w3zfn7faksuyydkoa2rp.jpg"
                                alt="Ảnh minh họa">
                        </div>
                        <div class="col-md-6 p-0">
                            <div class="card-body h-100 d-flex flex-column justify-content-center p-0">
                                <form id="otp-form" autocomplete="off" novalidate="novalidate"
                                    class="d-flex flex-column w-100 p-5 gap-5" method="POST"
                                    action="../core/verify_otp.php">
                                    <div>
                                        <p class="fw-bold fs-1 m-0 p-0">Xác thực tài khoản</p>
                                        <p class="fs-5 m-0 p-0">
                                            Mã OTP đã được gửi đến email: <strong><?= htmlspecialchars($email) ?></strong>
                                        </p>
                                    </div>
                                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

                                    <div class="form-floating">
                                        <input class="form-control bg-light px-3" id="otp_code" name="otp_code"
                                            placeholder="Nhập mã OTP" type="text" maxlength="6" required />
                                        <label class="w-100 px-0">
                                            <p class="bg-light px-3 w-100 text-truncate">Nhập mã OTP</p>
                                        </label>
                                    </div>

                                    <?php if (isset($_GET['resend']) && $_GET['resend'] === '1'): ?>
                                        <div class="alert alert-success"> Mã OTP mới đã được gửi lại!</div>
                                    <?php endif; ?>


                                    <div>
                                        <button id="otp-submit-button" class="btn btn-primary fw-bold w-100"
                                            name="submit" type="submit">Xác thực</button>
                                    </div>

                                    <div class="bg-white text-center">
                                        <p class="m-0">
                                            <a href="../core/send_otp.php?resend=1"
                                                class="link-underline link-underline-opacity-0 fw-semibold">Gửi lại mã OTP</a>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include('partials/footer.php'); ?>
    </div>
</body>

</html>
