import {addXmlhttpRequest, addAlertListener} from "../modules/functions.js";
import JustValidate from "just-validate";

function addSubmitListener(){
  let commentSection = document.getElementById('comment-section');
  let commentForm = document.getElementById('comment-form');

  commentForm.addEventListener('submit', (event) => {
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
addSubmitListener();
loadMoreComments();