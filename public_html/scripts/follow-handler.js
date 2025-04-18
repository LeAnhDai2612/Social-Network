import { getUserId, followUser, unfollowUser } from "./request-utils.js";

const getUserProfileId = () => {
  const url = window.location.href;
  const urlParams = new URLSearchParams(url);
  const id = urlParams.get("user_id");
  if (id) {
    return id;
  }
  const idMatch = url.match(/user_id=(\d+)/);
  if (idMatch) {
    return idMatch[1];
  }
  return null;
};

const showFollowToast = (followButton, isUnfollowing) => {
  const toast = document.getElementById("toast");

  const toastMessage = document.getElementById("toast-message");

  const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toast);

  const post = followButton.closest(".post");
  const posterDisplayName = post.querySelector(
    ".post-poster-display-name"
  ).textContent;
  const posterUsername = post.querySelector(
    ".post-poster-username"
  ).textContent;

  if (isUnfollowing) {
    toastMessage.textContent = `You have unfollowed ${posterDisplayName} (${posterUsername})`;
  } else {
    toastMessage.textContent = `You are now following ${posterDisplayName} (${posterUsername})`;
  }

  toastBootstrap.show();
};

const updateFollowersText = (countElement, action, isProfile) => {
  let currentFollowers = parseInt(countElement.textContent);

  if (action === "add") {
    currentFollowers++;
  } else if (action === "subtract") {
    currentFollowers--;
  }

  const followerText = currentFollowers === 1 ? "Follower" : "Followers";
  const text = isProfile
    ? `${currentFollowers} ${followerText}`
    : `${currentFollowers}`;

  countElement.textContent = text;
};

const handleFollowButton = async (userId, followedUserId, isUnfollowing) => {
  const sidebarCountElement = document.getElementById(
    "sidebar-user-following-count"
  );
  const profileCountElement = document.getElementById(
    "user-profile-followers-amount"
  );
  const profileFollowButton = document.getElementById(
    "user-profile-follow-button"
  );

  if (isUnfollowing) {
    if (profileFollowButton) {
      profileFollowButton.checked = true;
      updateFollowersText(profileCountElement, "subtract", true);
    }
    updateFollowersText(sidebarCountElement, "subtract", false);
    await unfollowUser(userId, followedUserId);
  } else {
    if (profileFollowButton) {
      profileFollowButton.checked = false;
      updateFollowersText(profileCountElement, "add", true);
    }
    updateFollowersText(sidebarCountElement, "add", false);
    await followUser(userId, followedUserId);
  }
};

const togglePostsFollowButton = (posterId) => {
  if (!posterId) return;

  const postFollowButtons = document.querySelectorAll(
    `.post[data-poster-id="${parseInt(posterId)}"] .post-follow-button`
  );

  postFollowButtons.forEach((button) => {
    const currentText = button.textContent.trim();

    if (currentText === "Follow") {
      button.innerHTML = `
        <i class="bi bi-person-dash d-flex align-items-center justify-content-center"></i>
        <p class="m-0">Unfollow</p>
      `;
    } else {
      button.innerHTML = `
        <i class="bi bi-person-plus d-flex align-items-center justify-content-center"></i>
        <p class="m-0">Follow</p>
      `;
    }
  });
};

document.addEventListener("DOMContentLoaded", async () => {
  const profileFollowButton = document.getElementById(
    "user-profile-follow-button"
  );
  const moreOptionsFollowButtons = document.querySelectorAll(
    ".post-follow-button"
  );

  let userId = null;

  try {
    userId = await getUserId();
  } catch (error) {
    console.error("Error:", error);
  }

  if (profileFollowButton) {
    profileFollowButton.addEventListener("change", async () => {
      const followedUserId = getUserProfileId();
      const isUnfollowing = profileFollowButton.checked;
      handleFollowButton(userId, followedUserId, isUnfollowing);
      togglePostsFollowButton(followedUserId);
    });
  }

  for (let i = 0; i < moreOptionsFollowButtons.length; i++) {
    const button = moreOptionsFollowButtons[i];
    button.addEventListener("click", () => {
      const isUnfollowing = button.textContent.trim() === "Unfollow";
      const post = button.closest(".post");
      const posterId = parseInt(post.dataset.posterId);
      handleFollowButton(userId, posterId, isUnfollowing);
      togglePostsFollowButton(posterId);
      showFollowToast(button, isUnfollowing);
    });
  }
});
