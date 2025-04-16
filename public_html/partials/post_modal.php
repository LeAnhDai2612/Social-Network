<?php
require_once '../core/db_functions.php';
$pdo = connect_to_db();
$current_user_id = $_SESSION['user_id'] ?? null;

$stmt = $pdo->prepare("SELECT id, display_name, username FROM users_table WHERE id != ?");
$stmt->execute([$current_user_id]);
$all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="modal fade" id="post-modal" tabindex="-1" role="dialog" aria-labelledby="post-modal-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="d-flex modal-header align-items-center justify-content-center">
        <p class="modal-title fw-semibold fs-5" id="post-modal-label">Đăng bài</p>
      </div>
      <form id="post-modal-form" action="execute_core_file.php?filename=process_post_submission.php" method="POST"
        enctype="multipart/form-data" autocomplete="off" novalidate="novalidate">
        <div class="modal-body d-flex flex-column align-items-center justify-content-between px-5 py-4">

          <!-- Vùng upload -->
          <label for="post-modal-media-picker" class="post-modal-image-container w-100 border rounded">
            <div class="upload-container d-flex flex-column align-items-center justify-content-center w-100 h-100">
              <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor"
                class="bi bi-cloud-upload mb-1" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                  d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z" />
                <path fill-rule="evenodd"
                  d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z" />
              </svg>
              <p class="upload-icon-text fs-6 m-0 fw-semibold">Tải Ảnh/Video</p>
              <p class="upload-icon-text fs-6 m-0"><small>Dung lượng tối đa 5 MB.</small></p>
            </div>

            <!-- Input file -->
            <input type="file" class="form-control-file d-none w-100 h-100"
              id="post-modal-media-picker"
              name="post_modal_media_picker"
              accept="image/*,video/mp4,video/webm">

            <!-- Preview ảnh (video sẽ không hiện preview) -->
            <img id="post-modal-preview" class="w-100 h-100 rounded d-none">
          </label>

          <!-- Hidden field post ID -->
          <input type="hidden" id="post-modal-post-id" name="post_modal_post_id" value="">

          <!-- Validate error -->
          <div id="errors-container_custom-post-modal-picture"></div>
          <label for="privacy" class="form-label fw-semibold">Quyền riêng tư:</label>
<select class="form-select" id="privacy" name="privacy" required>
  <option value="public">Công khai</option>
  <option value="followers">Chỉ người theo dõi</option>
  <option value="custom">Tùy chọn</option>
</select>

<div id="allowed-viewers-container" style="display:none;" class="mt-2">
  <label class="form-label">Chọn người được xem (nếu là tùy chọn):</label>
  <select multiple class="form-select" name="allowed_viewers[]">
    <?php foreach ($all_users as $user): ?>
      <option value="<?= $user['id'] ?>"><?= $user['display_name'] ?> (@<?= $user['username'] ?>)</option>
    <?php endforeach; ?>
  </select>
</div>

<script>
document.getElementById("privacy").addEventListener("change", function () {
  const viewerBox = document.getElementById("allowed-viewers-container");
  viewerBox.style.display = this.value === "custom" ? "block" : "none";
});
</script>

          <!-- Caption -->
          <div class="form-group w-100 mt-3">
            <textarea class="form-control" id="post-modal-caption" name="post_caption" rows="3"
              placeholder="Bạn đang nghĩ gì..."></textarea>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer w-100 px-5 py-4">
          <div class="d-flex gap-3 w-100 m-0 p-0">
            <button type="button" class="btn btn-outline-secondary w-100"
              data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary w-100" id="post-modal-submit-button">Chia sẻ</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
