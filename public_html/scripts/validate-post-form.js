let validator;

const setupValidation = (mode) => {
  const form = document.getElementById("post-modal-form");

  if (validator) {
    validator.destroy();
  }

  validator = new window.JustValidate("#post-modal-form")
    .addField(
      "#post-modal-media-picker",
      [
        {
          rule: "required",
        },
        {
          rule: "minFilesCount",
          value: mode === "edit" ? 0 : 1,
          errorMessage: "Vui lòng chọn ảnh hoặc video.",
        },
        {
          rule: "maxFilesCount",
          value: 1,
        },
        {
          rule: "files",
          value: {
            files: {
              extensions: ["jpeg", "jpg", "png", "bmp", "mp4", "webm"],
              maxSize: 5000000, // 5MB
              minSize: 10000,    // 10KB
              types: [
                "image/jpeg",
                "image/jpg",
                "image/png",
                "image/bmp",
                "video/mp4",
                "video/webm"
              ],
            },
          },
          errorMessage:
            "Tệp không hợp lệ. Chỉ chấp nhận ảnh (JPEG, PNG, BMP) hoặc video (MP4, WEBM) dưới 5MB.",
        },
      ],
      {
        errorsContainer: "#errors-container_custom-post-modal-picture",
      }
    )
    .addField("#post-modal-caption", [
      {
        rule: "maxLength",
        value: 2200,
      },
    ])
    .onSuccess((event) => {
      event.preventDefault();
      HTMLFormElement.prototype.submit.call(form);
    });
};

export { setupValidation };
