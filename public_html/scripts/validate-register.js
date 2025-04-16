import { checkExists } from "./request-utils.js";

document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("#register-form");
  new window.JustValidate("#register-form", {
    validateBeforeSubmitting: true,
    tooltip: {
      position: "top",
    },
  })
    .addField("#email", [
      {
        rule: "required",
        errorMessage: "Email trống",
      },
      {
        rule: "email",
      },
      {
        validator: (value) => () =>
          checkExists("email", value).then((exists) => !exists),
        errorMessage: "Email  đã tồn tại",
      },
    ])
    .addField("#phone-number", [
      {
        rule: "required",
        errorMessage: "Số điện thoại đang trống",
      },
      {
        rule: "minLength",
        value: 3,
        errorMessage: "Ít nhất 3 ký tự",
      },
      {
        rule: "maxLength",
        value: 15,
        errorMessage: "Tối đa 15 ký tự",
      },
      {
        rule: "number",
      },
      {
        validator: (value) => () =>
          checkExists("phone_number", value).then((exists) => !exists),
        errorMessage: "Số điện thoại đã tồn tại",
      },
    ])
    .addField(
      "#full-name",
      [
        {
          rule: "required",
          errorMessage: "Họ và tên đang trống",
        },
        {
          rule: "minLength",
          value: 3,
          errorMessage: "Ít nhất 3 ký tự",
        },
        {
          rule: "maxLength",
          value: 15,
          errorMessage: "Tối đa 15 ký tự",
        },
      ],
      {
        tooltip: {
          position: "right",
        },
      }
    )
    .addField(
      "#username",
      [
        // TODO: fix error message for regex;
        {
          rule: "required",
          errorMessage: "Tên đăng nhập đang trống",
        },
        {
          rule: "customRegexp",
          value: /^[a-zA-Z0-9._]+$/,
        },
        {
          rule: "minLength",
          value: 1,
          errorMessage: "Ít nhất 1 ký tự",
        },
        {
          rule: "maxLength",
          value: 15,
          errorMessage: "Tối đa 15 ký tự",
        },
        {
          validator: (value) => () =>
            checkExists("username", value).then((exists) => !exists),
          errorMessage: "Username unavailable. Try another.",
        },
      ],
      {
        tooltip: {
          position: "right",
        },
      }
    )
    .addField(
      "#password",
      [
        {
          rule: "required",
          errorMessage: "Mật khẩu đang trống",
        },
        {
          rule: "minLength",
          value: 3,
          errorMessage: "Mật khẩu tối thiểu 3 ký tự",
        },
        {
          rule: "password",
          errorMessage: "Ít nhất 1 ký tự viết hoa, 1 ký tự viết thường, 1 ký tự đặc biệt và 1 số",
        },
      ],
      {
        tooltip: {
          position: "right",
        },
      }
    )
    .onSuccess((event) => {
      event.preventDefault();
      HTMLFormElement.prototype.submit.call(form);
    });
});
