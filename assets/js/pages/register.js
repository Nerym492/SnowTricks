import JustValidate from "just-validate";
import {addFileValidationRules, addMobileMenuEvent} from "../modules/functions.js";

let profilePictureActions = document.querySelector('.profile-picture-actions');
let image = document.getElementById('profile-picture');
const defaultImageSrc = image.src;
const profilePictureFileId = 'registration_form_profilePictureFile';
const profilePictureFile = document.getElementById('registration_form_profilePictureFile');
const registerValidator = new JustValidate('#registration-form', {
  validateBeforeSubmitting: true,
  errorLabelCssClass: ['invalid-field-text'],
});
const observerOptions = {
  childList: true
}

let formErrorsObserver = new MutationObserver(function (mutations) {
  for (let mutation of mutations) {
    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
      mutation.addedNodes.forEach(addedNode => {
        if (addedNode.classList.contains('invalid-field-text') && addedNode.parentElement.id === 'profile-picture-group') {
          deleteFormError(profilePictureActions);
          profilePictureActions.appendChild(addedNode);
        } else if (addedNode.classList.contains('invalid-field-text')) {
          deleteFormError(addedNode.parentElement);
        }
      })
    }
  }
});

function resetFileInput() {
  document.getElementById(profilePictureFileId).remove();
  image.insertAdjacentElement('beforebegin', profilePictureFile);
  image.src = defaultImageSrc;
}

function deleteFormError(parentElement) {
  let formError = parentElement.querySelector('.form-error');
  if (formError) {
    formError.remove()
  }
}

document.getElementById('registration_form_profilePictureFile').addEventListener('change', (event) => {
  deleteFormError(profilePictureActions);
  // Load the file
  let file = event.target.files[0];

  if (file !== undefined) {
    image.src = URL.createObjectURL(file);
  } else {
    // Delete and recreate the input to reset the value
    resetFileInput();
  }
});

document.getElementById('delete-profile-picture').addEventListener('click', () => {
  resetFileInput();
});

registerValidator
  .addField('#' + profilePictureFileId, [
    addFileValidationRules(),
  ],)
  .addField('#registration_form_pseudo', [
    {
      rule: 'required',
      errorMessage: 'Please enter a pseudo.'
    },
    {
      rule: 'minLength',
      value: 3,
      errorMessage: 'The pseudo must be at least 3 characters long'
    },
    {
      rule: 'maxLength',
      value: 40,
      errorMessage: 'The pseudo must not exceed 40 characters.'
    },
  ],)
  .addField('#registration_form_mail', [
    {
      rule: 'required',
      errorMessage: 'Please enter an email.'
    },
    {
      rule: 'email',
      errorMessage: 'This email is not valid'
    },
  ],)
  .addField('#registration_form_plainPassword', [
    {
      rule: 'required',
      errorMessage: 'Please enter an password.'
    },
    {
      rule: 'strongPassword',
      errorMessage: 'Minimum eight characters, at least one uppercase letter, one lowercase letter, one number and ' +
        'one special character.',
    }
  ],)
  .addField('#registration_form_agreeTerms', [
    {
      rule: 'required',
      errorMessage: 'You should agree to our terms.',
    }
  ])


formErrorsObserver.observe(document.getElementById('profile-picture-group'), observerOptions);

registerValidator.onSuccess(function (event) {
  event.preventDefault();
  document.getElementById('registration-form').submit();
})

document.querySelectorAll('.form-floating, .form-check').forEach(group => {
  formErrorsObserver.observe(group, observerOptions)
  if (group.classList.contains('form-check')) {
    group.querySelector('input').addEventListener('change', (event) => {
      if (event.target.classList.contains('just-validate-success-field')) {
        deleteFormError(group);
      }
    })
  }
})

addMobileMenuEvent();