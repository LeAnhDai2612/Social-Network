import { addTransformationParameters } from "./utility-functions.js";
import { setupValidation } from "./validate-post-form.js";
import { getPost } from "./request-utils.js";

const showModal = (mode, postId = null) => {
  const modal = new bootstrap.Modal(document.getElementById("post-modal"));
  modal.show();

  document.getElementById(
    "errors-container_custom-post-modal-picture"
  ).innerHTML = "";

  document.getElementById("post-modal-caption").textContent = "";

  setupValidation(mode);

  if (mode === "edit") {
    document.getElementById("post-modal-post-id").value = postId;
    document.getElementById("post-modal-form").action =
      "execute_core_file.php?filename=process_post_edit.php";
    document.getElementById("post-modal-submit-button").textContent = "Done";
    document.getElementById("post-modal-label").textContent = "Edit post";
    document.getElementById("post-modal-preview").src = "";
    return;
  }

  document.getElementById("post-modal-form").action =
    "execute_core_file.php?filename=process_post_submission.php";
  document.getElementById("post-modal-submit-button").textContent = "Đăng bài";
  document.getElementById("post-modal-label").textContent = "Tao bài viết";
  document.getElementById("post-modal-preview").src = "";
};

document.addEventListener("DOMContentLoaded", () => {
  const headerPostModalTrigger = document.getElementById("post-modal-trigger");
  const postMediaInput = document.getElementById("post-modal-media-picker");
  const preview = document.getElementById("post-modal-preview");
  const uploadContainer = document.querySelector(".upload-container");
  const editPostButtons = document.querySelectorAll(".post-edit-button");

  if (headerPostModalTrigger) {
    headerPostModalTrigger.addEventListener("click", () => {
      showModal("create");
      document.body.classList.add("modal-open");
      preview.classList.add("d-none");
      uploadContainer.classList.remove("d-none");
      uploadContainer.classList.add("d-flex");
      postMediaInput.value = "";
    });
  }

  if (postMediaInput) {
    postMediaInput.addEventListener("change", (event) => {
      const input = event.target;

      if (input.files && input.files[0]) {
        const file = input.files[0];

        if (file.type.startsWith("video/")) {
          preview.classList.add("d-none");
          uploadContainer.classList.add("d-none");
        } else {
          const reader = new FileReader();
          reader.onload = (e) => {
            preview.src = e.target.result;
            preview.classList.remove("d-none");
            uploadContainer.classList.add("d-none");
          };
          reader.readAsDataURL(file);
        }
        return;
      }

      preview.src = "";
      uploadContainer.classList.add("d-flex");
    });
  }

  for (let i = 0; i < editPostButtons.length; i++) {
    const button = editPostButtons[i];

    button.addEventListener("click", (event) => {
      event.preventDefault();

      document.body.classList.add("modal-open");
      const post = button.closest(".post");
      const postId = post.dataset.postId;

      showModal("edit", postId);

      uploadContainer.classList.add("d-none");
      preview.classList.remove("d-none");

      getPost(postId)
        .then((postData) => {
          const postMedia = postData.media_path;
          const transform = "w_720/f_auto,q_auto:eco";
          const url = addTransformationParameters(postMedia, transform);

          document.getElementById("post-modal-caption").textContent =
            postData.caption;
          preview.setAttribute("src", url);
        })
        .catch((error) => {
          console.error(error);
        });
    });
  }
});
