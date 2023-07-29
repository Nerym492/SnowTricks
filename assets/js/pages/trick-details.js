import {
  addXmlhttpRequest,
  addAlertListener,
  addMobileMenuEvent,
} from "../modules/functions.js";
import JustValidate from "just-validate";

function addSubmitListener() {
  let commentSection = document.getElementById("comment-section");
  let commentForm = document.getElementById("comment-form");

  const commentValidator = new JustValidate("#comment-form", {
    validateBeforeSubmitting: true,
    errorLabelCssClass: ["invalid-field-text"],
  });

  commentValidator.addField("#comment_form_content", [
    {
      rule: "required",
      errorMessage: "The comment cannot be empty.",
    },
  ]);

  commentValidator.onFail(() => {
    deleteFormErrors();
  });

  commentValidator.onSuccess(function (event) {
    event.preventDefault();
    let formData = new FormData(commentForm);
    let trickName = document.querySelector(".trick-container h1").innerHTML;
    addXmlhttpRequest(
      "POST",
      "/comment/submitForm/" + trickName,
      formData,
      commentSection,
      () => {
        document.getElementById("comment_form_content").innerHTML = "";
        addAlertListener();
        addSubmitListener();
      },
    );
  });
}

function loadMoreComments() {
  let btnLoadMoreComments = document.getElementById("btn-load-more-comments");
  if (btnLoadMoreComments) {
    document
      .getElementById("btn-load-more-comments")
      .addEventListener("click", () => {
        let commentsLoaded = document.querySelectorAll(".comment").length;
        let commentList = document.getElementById("comment-list");
        let trickName = document.querySelector(".trick-container h1").innerHTML;
        addXmlhttpRequest(
          "GET",
          "/comments/loaded/" + commentsLoaded + "/loadMore/" + trickName,
          null,
          commentList,
          () => {
            loadMoreComments();
            addAlertListener();
          },
        );
      });
  }
}

function deleteFormErrors() {
  let commentContent = document.getElementById("comment-content");
  let formErrors = commentContent.querySelectorAll(
    ".form-error, .invalid-field-text",
  );

  if (formErrors.length > 1) {
    formErrors[0].remove();
  }
}

addAlertListener();
if (document.getElementById("comment_form_content")) {
  addSubmitListener();
}
loadMoreComments();
addMobileMenuEvent();
