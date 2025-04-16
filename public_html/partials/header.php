<nav class="header navbar navbar-light fixed-top bg-white w-100 border-bottom-1 m-0 p-0 border-bottom flex-nowrap">
    <div class="container-fluid d-flex justify-content-between w-100 pt-4 pe-5 pb-4 ps-5">
        <a class="navbar-brand d-flex align-items-center justify-content-center align-self-start gap-2"
            href="index.php">
            <img src="images/logo-icon.svg" width="28" height="28" class="d-inline-block align-top" alt="">
            <p class="h4 m-0 p-0">Social Network</p>
        </a>

        <button id="dark-mode-toggle" class="btn btn-outline-dark ms-3" style="font-size: 18px;">
        </button>

        <div class="form-group has-search d-flex align-items-center flex-column">
            <form id="search-bar-form" autocomplete="off">
                <div class="autocomplete d-flex align-items-center" style="width:300px;">
                    <span class="bi bi-search form-control-feedback"></span>
                    <input id="search-bar" type="search" class="form-control mr-sm-2 bg-light" placeholder="Tìm kiếm"
                        aria-label="Search">
                </div>
                <ul id="search-results"
                    class="d-flex flex-column align-items-center justify-content-center m-0 p-0 mt-2 p-2 border rounded gap-2 hidden">
                </ul>
            </form>
        </div>
<!-- 🔔 THÔNG BÁO DROPDOWN -->
<div class="dropdown me-3">
    <button class="btn btn-light position-relative dropdown-toggle" type="button"
        id="dropdownNotification" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-bell fs-5"></i>
        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_name('USER_SESSION');
            session_start();
        }
        require_once '../core/config.php';

        if (isset($_SESSION['user_id'])) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
            $stmt->execute([$_SESSION['user_id']]);
            $unread = $stmt->fetchColumn();
            if ($unread > 0) {
                echo "<span class='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger'>$unread</span>";
            }
        }
        ?>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow p-2"
        style="width: 350px; max-height: 400px; overflow-y: auto;" aria-labelledby="dropdownNotification">
        <?php
        if (isset($_SESSION['user_id'])) {
            $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
            $stmt->execute([$_SESSION['user_id']]);
            $notis = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($notis)) {
                echo "<li class='text-center text-muted small px-2'>Không có thông báo</li>";
            } else {
                foreach ($notis as $n) {
                    $link = "#";
                    if ($n['type'] === 'like' && !empty($n['post_id'])) {
                        $link = "index.php?post_id=" . $n['post_id'];
                    } elseif ($n['type'] === 'reply' && !empty($n['post_id'])) {
                        $link = "index.php?post_id=" . $n['post_id'] . "#comment-" . $n['comment_id'];
                    }

                    echo "<li><a class='dropdown-item small ".($n['is_read'] ? '' : 'fw-bold')."' href='$link'>" .
                        htmlspecialchars($n['content']) . "</a></li>";
                }
            }
            echo "<li><hr class='dropdown-divider'></li>";
            echo "<li><a href='notifications.php' class='dropdown-item text-center fw-semibold'>Xem tất cả</a></li>";
        }
        ?>
    </ul>
</div>

        <!-- 🔔 KẾT THÚC THÔNG BÁO -->

        <button type="button"
            class="btn btn-primary post-button-custom d-flex align-items-center justify-content-center text-nowrap fw-semibold"
            data-toggle="modal" data-target="#post-modal" id="post-modal-trigger">
            <i class="bi bi-plus fs-4 d-flex me-1"></i>
            Đăng bài
        </button>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</nav>
