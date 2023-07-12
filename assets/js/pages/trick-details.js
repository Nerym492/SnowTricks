import {addXmlhttpRequest, addAlertListener} from "../modules/functions.js";
import JustValidate from "just-validate";

function addSubmitListener(){
  let commentSection = document.getElementById('comment-section');
  let commentForm = document.getElementById('comment-form');

  const commentValidator = new JustValidate('#comment-form', {
    validateBeforeSubmitting: true,
    errorLabelCssClass: ['invalid-field-text'],
  });

  commentValidator.addField('#comment_form_content', [
    {
      rule: 'required',
      errorMessage: 'The comment cannot be empty.'
    }
  ])

  commentValidator.onSuccess(function (event) {
    event.preventDefault();
    let formData = new FormData(commentForm);
    addXmlhttpRequest('POST','/comment/submitForm', formData, commentSection, () => {
      document.getElementById('comment_form_content').innerHTML = '';
      addAlertListener();
      addSubmitListener();
    })
  })
}

function loadMoreComments() {
  document.getElementById('btn-load-more-comments').addEventListener('click', () => {
    let commentsLoaded = document.querySelectorAll('.comment').length;
    let commentList = document.getElementById('comment-list');
    addXmlhttpRequest('GET', '/comments/loaded/'+commentsLoaded+'/loadMore/', null, commentList, () => {
      loadMoreComments();
      addAlertListener();
    })
  })
}

addAlertListener();
if (document.getElementById('comment_form_content')) {
  addSubmitListener();
}
loadMoreComments();