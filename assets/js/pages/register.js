import JustValidate from "just-validate";

let image = document.getElementById('profile-picture');
const defaultImageSrc = image.src;
const profilePictureFileId = 'registration_form_profilePictureFile';
const profilePictureFile = document.getElementById('registration_form_profilePictureFile');

function resetFileInput(){
  document.getElementById(profilePictureFileId).remove();
  image.insertAdjacentElement('beforebegin', profilePictureFile);
  image.src = defaultImageSrc;
}

document.getElementById('registration_form_profilePictureFile').addEventListener('change', (event) => {
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