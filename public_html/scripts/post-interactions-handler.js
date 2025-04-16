import { addLike, removeLike, getUserId } from "./request-utils.js";

setTimeout(() => {
  document.body.className = "";
}, 100);

const updateLikesText = (post, addOrSubtract) => {
  const likesText = post.querySelector(".like-text");
  if (!likesText) return; // Kiểm tra likesText có tồn tại không
  const currentLikes = parseInt(likesText.textContent);

  if (addOrSubtract === "add") {
    const newLikes = currentLikes + 1;
    likesText.textContent = `${newLikes} like${newLikes !== 1 ? "s" : ""}`;
  } else if (addOrSubtract === "subtract") {
    const newLikes = Math.max(currentLikes - 1, 0);
    likesText.textContent = `${newLikes} like${newLikes !== 1 ? "s" : ""}`;
  }
};

const handleDoubleClickLike = async (post, likeButton) => {
  const isLiked = likeButton?.checked;

  if (isLiked) {
    return;
  }

  getUserId()
    .then((userId) => {
      const postId = post.dataset.postId;
      updateLikesText(post, "add");
      addLike(userId, postId);
    })
    .catch((error) => {
      console.error("Error:", error);
    });

  likeButton.checked = true;
};

const handleLikeButtonClick = async (post, likeButton) => {
  const isUnliked = !likeButton?.checked;

  getUserId()
    .then((userId) => {
      const postId = post.dataset.postId;

      if (isUnliked) {
        updateLikesText(post, "subtract");
        removeLike(userId, postId);
        return;
      }

      updateLikesText(post, "add");
      addLike(userId, postId);
    })
    .catch((error) => {
      console.error("Error:", error);
    });
};

document.addEventListener("DOMContentLoaded", () => {
  const posts = document.querySelectorAll(".post");

  posts.forEach((post) => {
    const postImage = post.querySelector(".feed-post-image");
    const likeButton = post.querySelector(".like");

    if (postImage) {
      postImage.addEventListener("dblclick", () =>
        handleDoubleClickLike(post, likeButton)
      );
    }

    if (likeButton) {
      likeButton.addEventListener("click", () =>
        handleLikeButtonClick(post, likeButton)
      );
    }
  });
});
