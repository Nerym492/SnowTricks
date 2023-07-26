import JustValidate from "just-validate";
import { addMobileMenuEvent } from "../modules/functions";

const formValidatorConfig = {
  validateBeforeSubmitting: true,
  errorLabelCssClass: ["invalid-field-text"],
};

const resetPassValidator = new JustValidate(
  "#reset_password_form",
  formValidatorConfig,
);

if (document.getElementById("reset_password_request_form_mail")) {
  resetPassValidator.addField("#reset_password_request_form_mail", [
    {
      rule: "required",
      errorMessage: "Please enter your email address.",
    },
    {
      rule: "email",
      errorMessage: "Your email address is not valid.",
    },
  ]);
}

if (document.getElementById("reset_password_form_password")) {
  resetPassValidator.addField("#reset_password_form_password", [
    {
      rule: "strongPassword",
    },
  ]);
}

resetPassValidator.onSuccess(function (event) {
  event.preventDefault();
  document.getElementById("reset_password_form").submit();
});

resetPassValidator.onFail(() => {
  let validationErrors = document.querySelectorAll(".form-error");
  validationErrors.forEach((error) => {
    // Remove errors sent by the server
    error.remove();
  });
});

addMobileMenuEvent();
