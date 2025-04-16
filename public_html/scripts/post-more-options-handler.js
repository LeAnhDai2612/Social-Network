import { deletePost } from "./request-utils.js";
import { getBaseUrl } from "./utility-functions.js";

const copyToClipboard = (text) => {
  const tempInput = document.createElement("input");
  tempInput.value = text;
  document.body.appendChild(tempInput);
  tempInput.select();
  navigator.clipboard.writeText(tempInput.value);
  document.body.removeChild(tempInput);
};

const handleDeletePosts = () => {
  const deletePostButtons = document.querySelectorAll(".delete-post-button");
  const confirmDeleteButton = document.getElementById("confirm-delete-post");
  const modalConfirmDeleteEl = document.getElementById("modal-confirm-delete-post");

  if (!modalConfirmDeleteEl) return;
  const modalInstance = bootstrap.Modal.getOrCreateInstance(modalConfirmDeleteEl);

  let selectedPostElement = null;
  let triggerElement = null;

  deletePostButtons.forEach((button) => {
    button.addEventListener("click", () => {
      selectedPostElement = button.closest(".post");
      triggerElement = button;

      if (!selectedPostElement) return;

      confirmDeleteButton.dataset.postId = selectedPostElement.dataset.postId;
      modalInstance.show();
    });
  });

  confirmDeleteButton.addEventListener("click", (e) => {
    e.preventDefault();
    const postId = confirmDeleteButton.dataset.postId;

    if (!postId) return;

    deletePost(postId)
      .then((result) => {
        if (result?.success) {
          const postToRemove = document.querySelector(`.post[data-post-id="${postId}"]`);
          if (postToRemove) postToRemove.remove();
          if (triggerElement) triggerElement.focus();
          modalInstance.hide();
        } else {
          alert(result?.error || "Xoá bài viết thất bại.");
        }
      })
      .catch((error) => {
        alert("Lỗi kết nối khi xoá bài viết.");
        console.error("Lỗi khi xoá bài viết:", error);
      });
  });

  modalConfirmDeleteEl.addEventListener('hidden.bs.modal', () => {
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) backdrop.remove();
      document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
      if (triggerElement && document.body.contains(triggerElement)) {
      triggerElement.focus();
    }
      selectedPostElement = null;
    triggerElement = null;
    confirmDeleteButton.removeAttribute('data-postId');
  });
  
};

const handleCopyLinks = () => {
  const copyLinkButtons = document.querySelectorAll(".post-copy-link-button");
  const toast = document.getElementById("toast");
  const toastMessage = document.getElementById("toast-message");

  copyLinkButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const baseUrl = getBaseUrl();
      const post = button.closest(".post");
      if (!post) return;
      const postId = post.dataset.postId;
      const postLink = `${baseUrl}/index.php?post_id=${postId}`;
      copyToClipboard(postLink);

      if (toast && toastMessage) {
        toastMessage.textContent = 'Đã sao chép liên kết!';
        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toast);
        toastBootstrap.show();
      } else {
        alert('Đã sao chép liên kết!');
      }
    });
  });
};

document.addEventListener("DOMContentLoaded", () => {
  handleDeletePosts();
  handleCopyLinks();
});
