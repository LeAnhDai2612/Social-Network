export const getBaseUrl = () => {
  const protocol = window.location.protocol.toLowerCase().split(":")[0];
  const host = window.location.host;
  const path = window.location.pathname.split("/").slice(0, -1).join("/");
  return `${protocol}://${host}${path}`;
};

export const addTransformationParameters = (imageUrl, transformation) => {
  const urlParts = imageUrl.split("/upload/");
  if (urlParts.length === 2) {
    return urlParts[0] + "/upload/" + transformation + "/" + urlParts[1];
  }
  return imageUrl;
};

// Dark mode toggle vẫn hoạt động:
document.addEventListener("DOMContentLoaded", () => {
  if (localStorage.getItem("dark-mode") === "true") {
    document.body.classList.add("dark-mode");
  }

  const toggleBtn = document.getElementById("dark-mode-toggle");
  if (toggleBtn) {
    toggleBtn.addEventListener("click", function () {
      document.body.classList.toggle("dark-mode");
      localStorage.setItem("dark-mode", document.body.classList.contains("dark-mode"));
      toggleBtn.innerText = document.body.classList.contains("dark-mode") ? "🌙" : "🌞";
    });

    toggleBtn.innerText = document.body.classList.contains("dark-mode") ? "🌙" : "🌞";
  }
});
