const fileInputs = document.querySelectorAll('.trick-form-file');

function displayImagePreview(files, preview){
  if (files && files.length > 0) {
    const file = files[0];
    // Retrieve selected file
    const reader = new FileReader();

    reader.onloadend = function () {
      const img = document.createElement('img');
      img.src = reader.result;
      img.classList.add('image-trick-details');

      preview.innerHTML = '';
      preview.appendChild(img);
    };

    reader.readAsDataURL(file);
  } else {
    preview.innerHTML = '';
  }
}

fileInputs.forEach(fileInput => {
  const preview = fileInput.parentElement.firstElementChild;

  // Preview image as it changes.
  fileInput.addEventListener('change', function (event) {
    const files = event.target.files;
    displayImagePreview(files, preview);
  });
});