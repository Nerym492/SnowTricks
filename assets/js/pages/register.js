import JustValidate from "just-validate";
document.getElementById('registration_form_profilePictureFile').addEventListener('change', (event) => {
  // Load the file
  let image = document.getElementById('profile-picture');
  image.src = URL.createObjectURL(event.target.files[0]);
})