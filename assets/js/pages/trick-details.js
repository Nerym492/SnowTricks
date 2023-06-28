import {addXmlhttpRequest, addAlertListener} from "../modules/functions.js";
import JustValidate from "just-validate";

let commentSection = document.getElementById('comment-section');
const commentForm = document.getElementById('comment-form');
commentForm.addEventListener('submit', (event) => {
  event.preventDefault();
  let formData = new FormData(commentForm);
  addXmlhttpRequest('POST','/comment/submitForm', formData, commentSection, () => {
    document.getElementById('comment_form_content').innerHTML = '';
    addAlertListener();
  })
})

addAlertListener();