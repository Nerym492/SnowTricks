import JustValidate from "just-validate";

const fileInputs = document.querySelectorAll('.trick-form-file');
const imagePlaceholder = "<img class=\"image-trick-details\" src=\"/build/images/image-placeholder.webp\" alt=\"\">";
let imagesCollection = document.getElementById('images-list');
let index = imagesCollection.children.length - 1;
let addImageFormButton = document.getElementById('add-image-form-button');


const trickValidator = new JustValidate('#trick_form', {
  validateBeforeSubmitting: true,
});

function addInputChangeListener (fileInput, preview) {
  fileInput.addEventListener('change', function (event) {
    const files = event.target.files;
    if (fileInput.classList.contains('just-validate-error-field')) {
      preview.innerHTML = imagePlaceholder;
    } else {
      displayImagePreview(files, preview);
    }
  })
}

function addInputFileValidation(input) {
  trickValidator.addField("#" + input.id, [
    {
      rule: 'minFilesCount',
      value: 0,
    },
    {
      rule: 'maxFilesCount',
      value: 1,
    },
    {
      rule: 'files',
      value: {
        files: {
          types: ['image/png', 'image/webp', 'image/jpeg'],
          extensions: ['png', 'jpeg', 'jpg', 'webp'],
          maxSize: 600000,
          minSize: 5000,
        },
      },
      errorMessage: 'The file must be an image (png, jpeg, jpg, webp). Maximum size 600kb'
    },
  ]);
}

function displayImagePreview(files, preview) {
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
    preview.innerHTML = imagePlaceholder;
  }
}

function addImageForm(imagePlaceholder) {
  // Retrieves the prototype of the collection field.
  let prototype = imagesCollection.dataset.prototype;

  let newForm = prototype.replace(/__name__/g, index);
  // Increment index for next addition.
  index++;

  let tempDiv = document.createElement('div');
  tempDiv.innerHTML = newForm;
  tempDiv.firstElementChild.classList.add('trick-image-item');
  // Create new input preview
  let newPreview = document.createElement('div');
  newPreview.classList.add('trick-image-preview');
  newPreview.innerHTML = imagePlaceholder;

  // Retrieves new form id.
  let newFormId = tempDiv.firstChild.id;
  // Insert the new preview inside the tempDiv
  tempDiv.firstChild.insertBefore(newPreview, tempDiv.firstChild.firstChild)

  imagesCollection.insertBefore(tempDiv.firstChild, addImageFormButton);

  const newFileInput = document.getElementById(newFormId).lastElementChild.firstChild;
  addInputFileValidation(newFileInput);
  addInputChangeListener(newFileInput, newPreview);
}

addImageFormButton.addEventListener('click', function () {
  addImageForm(imagePlaceholder);
})


fileInputs.forEach(fileInput => {
  const preview = fileInput.closest('.trick-image-item').querySelector('.trick-image-preview');
  addInputFileValidation(fileInput);
  // Preview image as it changes.
  addInputChangeListener(fileInput, preview);
});

