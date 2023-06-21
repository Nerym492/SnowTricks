import JustValidate from "../modules/just-validate";

const headerImage = document.getElementById('image-header-details');
const trickForm = document.getElementById('trick_form');
const fileInputs = document.querySelectorAll('.trick-form-file');
const imagePlaceholderSrc = '/build/images/image-placeholder.webp';
const imagePlaceholder = '<img class="image-trick-details img-form" src="'+imagePlaceholderSrc+'" alt="">';
const videosLinks = document.querySelectorAll('.trick-video-link');
const videoPlaceholderSrc = '/build/images/video-placeholder.png';
const videoPlaceholder = '<img class="image-trick-details img-form" src="'+videoPlaceholderSrc+'" alt="">';
let imagesCollection = document.getElementById('images-list');
let imagesCollectionIndex = imagesCollection.children.length - 1;
let addImageFormButton = document.getElementById('add-image-form-button');
let isProcessing = false;

const bordersHtml = '<img class="border-isheader isheader-top" src="/build/images/border-top-isheader.png"\n' +
                          'alt="border top isheader" id="border-isheader-top">\n' +
                    '<img class="border-isheader isheader-right" src="/build/images/border-right-isheader.png"\n' +
                          'alt="border right isheader" id="border-isheader-right">\n' +
                    '<img class="border-isheader isheader-bot" src="/build/images/border-bot-isheader.png"\n' +
                          'alt="border bot isheader" id="border-isheader-bot">\n' +
                    '<img class="border-isheader isheader-left" src="/build/images/border-left-isheader.png"\n' +
                          'alt="border left isheader" id="border-isheader-left">'

const borderFadeOut = 'border-fade-out'
const animationsBorders = {
  'top': {'fadeOut': borderFadeOut, 'fadeIn': 'border-fade-in-down'},
  'right': {'fadeOut': borderFadeOut, 'fadeIn': 'border-fade-in-right'},
  'bot': {'fadeOut': borderFadeOut, 'fadeIn': 'border-fade-in-up'},
  'left': {'fadeOut': borderFadeOut, 'fadeIn': 'border-fade-in-left'},
}

console.log(animationsBorders);

// Actions performed when adding elements to another
let borderObserver = new MutationObserver(function (mutationsList) {
  for (let mutation of mutationsList) {
    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
      let addedElement = mutation.addedNodes[0];
      let borderName = addedElement.classList[1].split('-')[1];
      // isHeader input is updated for the first mutation only
      if (borderName === 'top') {
        let allInputsIsHeader = document.querySelectorAll('.trick-form-isheader');
        allInputsIsHeader.forEach(input => {
          input.value = '0';
        })
        let inputIsHeader = addedElement.closest('.trick-image-item').querySelector('.trick-form-isheader');
        inputIsHeader.value = '1';
      }
      animateBorder(addedElement, animationsBorders[borderName].fadeIn, 'fadeIn',
        addedElement.closest('.favorite-image'));
    }
  }
});

let observerOptions = {
  childList: true,
}

// Checks for attribute changes
let imageHeaderObserver = new MutationObserver(function (mutations){
  if (isProcessing) {
    // Ignore additional mutations while processing is in progress
    return;
  }

  isProcessing = true;

  // Retrieve only the last mutation in the list
  let lastMutation = mutations[mutations.length - 1];

  if (lastMutation.type === 'attributes' && lastMutation.attributeName === 'src') {
    // Traiter la dernière mutation
    animateElement(headerImage, 'header-img-zoom-in', ()=>{});
  }

  // Reset isProcessing flag after 200 ms delay
  setTimeout(function() {
    isProcessing = false;
  }, 200);
})

const trickValidator = new JustValidate('#trick_form', {
  validateBeforeSubmitting: true,
  errorLabelCssClass: ['invalid-field-text'],
});

function addInputChangeListener(fileInput, preview) {
  fileInput.addEventListener('change', function (event) {
    const files = event.target.files;
    // For files that already had a preview when the form was loaded
    if (fileInput.classList.contains("isFilled")) {
      addInputFileValidation(fileInput);
      trickValidator.revalidateField("#" + fileInput.id).then(isValid => {
        if (isValid) {
          displayImagePreview(files, preview);
        } else {
          preview.innerHTML = imagePlaceholder;
        }
      })
    } else {
      if (event.target.classList.contains('just-validate-error-field')) {
        let borders = preview.querySelectorAll('.border-isheader');
        let tempDiv = document.createElement('div')
        borders.forEach(border => {
          tempDiv.appendChild(border);
        })
        preview.innerHTML = imagePlaceholder;
        if (borders.length > 0) {
          reassignIsHeaderImage(tempDiv);
        }
      } else {
        displayImagePreview(files, preview);
      }
    }
  })
}

function addDeleteListener(deleteButton) {
  deleteButton.addEventListener('click', function () {
    let trickImgItem = deleteButton.closest('.trick-image-item');
    let isHeaderBorders = trickImgItem.querySelectorAll('.border-isheader')
    let tempDiv = document.createElement('div');
    // This image is currently the image header
    // Storing images borders in a temp div
    if (isHeaderBorders.length > 0) {
      isHeaderBorders.forEach(border => {
        tempDiv.appendChild(border);
      })
    }

    // Selects all containers following to the item that has been deleted
    let nextItem = trickImgItem.nextElementSibling;
    let remainingItems = [nextItem];

    while (nextItem) {
      nextItem = nextItem.nextElementSibling
      if (nextItem) {
        remainingItems.push(nextItem);
      }
    }
    trickImgItem.classList.add('img-item-zoom-out')


    animateElement(trickImgItem, 'img-item-zoom-out', () => {
      trickImgItem.remove();
      // Rearrange the following elements to fill the empty space
      remainingItems.forEach((item, index) => {
        item.style.transitionDelay = `${index * 0.1}s`;
      });

      if (tempDiv.innerHTML !== '') {
        // Borders are automatically reassign to the first valid image of the collection
        // New isHeaderImage is set
        reassignIsHeaderImage(tempDiv)
      }
    })
  })
}

function addInputFileValidation(input) {
  trickValidator.addField("#" + input.id, [
    {
      rule: 'minFilesCount',
      value: 1,
      errorMessage: 'Please select a file or click on the trash can icon',
    },
    {
      rule: 'maxFilesCount',
      value: 1,
      errorMessage: 'Please select a file or click on the trash can icon',
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
    // Contains a border image if header image is defined
    let borderIsHeader = preview.closest('.medias-container').querySelectorAll('.border-isheader');
    const file = files[0];
    // Retrieve selected file
    const reader = new FileReader();

    reader.onloadend = function () {
      const img = document.createElement('img');
      img.src = reader.result;
      img.classList.add('image-trick-details', 'img-form');

      preview.querySelector('.image-trick-details').remove();
      preview.appendChild(img);
      addLinkFavoriteImage(preview);
      // Add borders and set this image to favorite
      if (borderIsHeader.length === 0) {
        preview.querySelector('.favorite-image').insertAdjacentHTML('afterbegin', bordersHtml);
      }
    };

    reader.readAsDataURL(file);
  } else {
    preview.innerHTML = imagePlaceholder;
  }
}

function addLinkFavoriteImage(preview) {
  let image = preview.querySelector('.image-trick-details');

  let link = document.createElement('a');
  link.setAttribute('role', 'button');
  link.setAttribute('class', 'favorite-image');
  link.appendChild(image.cloneNode(true));

  image.parentNode.replaceChild(link, image);

  let linkFavoriteImg = preview.querySelector('.favorite-image');
  linkFavoriteImg.addEventListener('click', () => {
    moveFavoriteImgBorders(linkFavoriteImg);
  })
  borderObserver.observe(linkFavoriteImg, observerOptions);
}

function reassignIsHeaderImage(tempDiv) {
  let otherFavoriteImages = document.querySelectorAll('.favorite-image');
  // Create an array with the NodeList
  let borders = Array.from(tempDiv.childNodes);
  if (otherFavoriteImages.length > 0) {
    let lastFavoriteImg = otherFavoriteImages[0]
    borders.forEach(border => {
      lastFavoriteImg.appendChild(border);
    })
  }
  // No image defined as header
  if (otherFavoriteImages.length === 0) {
    document.getElementById('image-header-details').src = imagePlaceholderSrc;
  }
}

function moveFavoriteImgBorders(linkFavoriteImg) {
  let borderTop = document.getElementById('border-isheader-top');
  let borderRight = document.getElementById('border-isheader-right');
  let borderBot = document.getElementById('border-isheader-bot');
  let borderLeft = document.getElementById('border-isheader-left');
  // Select all borders
  animateBorder(borderTop, animationsBorders.top.fadeOut, 'fadeOut', linkFavoriteImg);
  animateBorder(borderRight, animationsBorders.right.fadeOut, 'fadeOut', linkFavoriteImg);
  animateBorder(borderBot, animationsBorders.left.fadeOut, 'fadeOut', linkFavoriteImg);
  animateBorder(borderLeft, animationsBorders.bot.fadeOut, 'fadeOut', linkFavoriteImg);
}

function animateBorder(border, animation, animationType, linkFavoriteImg) {

  border.classList.add(animation);
  border.addEventListener('animationend', function afterFadeOutHandler(event) {
    if (animationType === 'fadeOut') {
      linkFavoriteImg.appendChild(border);
    } else if (animationType === 'fadeIn') {
      // Set the image header
      let headerImage = document.getElementById('image-header-details');
      headerImage.src = linkFavoriteImg.querySelector('.image-trick-details').src
    }

    border.classList.remove(animation);
    event.currentTarget.removeEventListener('animationend', afterFadeOutHandler);
  })
}

function animateElement(element, animation, actionAfterAnimation) {
  element.classList.add(animation)
  element.addEventListener('animationend', function animationEndHandler (event) {
    if (actionAfterAnimation !== 'undefined') {
      actionAfterAnimation();
    }
    element.classList.remove(animation);
    event.currentTarget.removeEventListener('animationend', animationEndHandler);
  })
}

function addImageForm(imagePlaceholder) {
  // Retrieves the prototype of the collection field.
  let prototype = imagesCollection.dataset.prototype;

  let newForm = prototype.replace(/__name__/g, imagesCollectionIndex);
  // Increment imagesCollectionIndex for next addition.
  imagesCollectionIndex++;

  let tempDiv = document.createElement('div');
  tempDiv.innerHTML = newForm;
  tempDiv.firstElementChild.classList.add('trick-image-item');
  // Create new input preview
  let newPreview = document.createElement('div');
  newPreview.classList.add('trick-image-preview');
  newPreview.innerHTML = imagePlaceholder;
  // New delete button HTML
  let deleteButtonHtml = '<a role="button" class="delete-image-btn" id="delete-img-btn-' + imagesCollectionIndex + '">\n' +
    '<i class=\"fa-solid fa-trash icon-delete-img\"></i>\n' +
    '</a>'

  // Retrieves new form id.
  let newFormId = tempDiv.firstChild.id;
  // Insert the new preview inside the tempDiv
  tempDiv.firstChild.insertBefore(newPreview, tempDiv.firstChild.firstChild)
  animateElement(tempDiv.firstChild, 'img-item-zoom-in', ()=>{})

  imagesCollection.insertBefore(tempDiv.firstChild, addImageFormButton);
  const trickImgItem = document.getElementById(newFormId);
  const newFileInput = trickImgItem.querySelector('.trick-form-file');
  // Insert the new delete button
  newFileInput.parentElement.insertAdjacentHTML('beforeend', deleteButtonHtml)
  // Select delete button after insertion
  let deleteButton = document.getElementById("delete-img-btn-" + imagesCollectionIndex)
  addDeleteListener(deleteButton)
  addInputFileValidation(newFileInput);
  addInputChangeListener(newFileInput, newPreview);
  // isTheHeader field set to 0 by default
  trickImgItem.querySelector('.trick-form-isheader').value = '0'
}

addImageFormButton.addEventListener('click', function () {
  addImageForm(imagePlaceholder);
})

fileInputs.forEach(fileInput => {
  //Retrieving the parent group with the image, preview and buttons
  const imageItem = fileInput.closest('.trick-image-item');
  const preview = imageItem.querySelector('.trick-image-preview');
  const linkFavoriteImg = preview.querySelector('.favorite-image');

  linkFavoriteImg.addEventListener('click', () => {
    moveFavoriteImgBorders(linkFavoriteImg);
  })

  if (fileInput.classList.contains("isFilled") === false) {
    addInputFileValidation(fileInput);
  }
  //Preview image as it changes.
  addInputChangeListener(fileInput, preview);
  addDeleteListener(imageItem.querySelector('.delete-image-btn'));
  borderObserver.observe(linkFavoriteImg, observerOptions);
});

videosLinks.forEach(link => {
  trickValidator
    .addField('#'+link.id, [
      {
        rule: 'required',
        errorMessage: 'The link cannot be empty'
      },
      {
        rule: 'customRegexp',
        value: /^https?:\/\/(?:www\.)?youtube\.com\/embed\/[A-Za-z0-9_-]{11}$/,
        errorMessage: 'The link is not valid',
      }
    ])
})

trickValidator
  .addField('#trick_form_name', [
    {
      rule: 'required',
      errorMessage: 'Please enter a trick name',
    }
  ])
  .addField('#trick_form_description', [
    {
      rule: 'required',
      errorMessage: 'Please enter a description',
    }
  ])
  .addField('#trick_form_group_trick', [
    {
      rule: 'required',
      errorMessage: 'Please select a group',
    }
  ])

trickValidator.onSuccess(function (event) {
  event.preventDefault();
  trickForm.submit();
  console.log('hello !!!')
});

imageHeaderObserver.observe(headerImage, { attributes: true, attributeFilter: ['src']})


