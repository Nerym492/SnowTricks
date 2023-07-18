import JustValidate from "just-validate";
import {addMobileMenuEvent} from "../modules/functions";


const headerImage = document.getElementById('image-header-details');
const trickForm = document.getElementById('trick_form');
const fileInputs = document.querySelectorAll('.trick-form-file');
const imagePlaceholderSrc = '/build/images/image-placeholder.webp';
const imagePlaceholder = '<img class="image-trick-details img-form" src="' + imagePlaceholderSrc + '" alt="">';
const addImageFormButton = document.getElementById('add-image-form-button');
let imagesCollection = {
  'index': document.getElementById('images-list').children.length - 1,
  'class': 'trick-image-item',
  'previewClass': 'trick-image-preview',
  'data': document.getElementById('images-list'),
}

const videosLinks = document.querySelectorAll('.trick-video-link');
const videoPlaceholderSrc = '/build/images/video-placeholder.png';
const videoPlaceholder = '<img class="image-trick-details img-form" src="' + videoPlaceholderSrc + '" alt="">';
let videoCollection = {
  'index': document.getElementById('videos-list').children.length - 1,
  'class': 'trick-video-item',
  'previewClass': 'trick-video-preview',
  'data': document.getElementById('videos-list'),
}
const addVideoFormButton = document.getElementById('add-video-form-button');


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
let previewObserver = new MutationObserver(function (mutationsList) {
  for (let mutation of mutationsList) {
    if (mutation.type === 'childList' && mutation.addedNodes.length > 0 && mutation.addedNodes.length < 7
      && mutation.addedNodes[0].classList.contains('border-isheader')) {
      // Borders are moved from one preview to another
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
        addedElement.closest('.trick-image-preview'))

    } else if (mutation.type === 'childList' && mutation.addedNodes.length === 7
      && mutation.addedNodes[0].classList.contains('border-isheader')) {
      // The borders have been added using the bordersHtml variable.(not moved from another preview)
      mutation.addedNodes.forEach(addedNode => {
        if (addedNode.tagName === 'IMG') {
          let borderName = addedNode.classList[1].split('-')[1];
          animateBorder(addedNode, animationsBorders[borderName].fadeIn, 'fadeIn',
            addedNode.closest('.trick-image-preview'))
        }
      })
      mutation.addedNodes[0].closest('.trick-image-item').querySelector('.trick-form-isheader').value = '1';
    } else if (mutation.type === 'childList' && mutation.addedNodes.length > 0
      && mutation.target === mutation.addedNodes[0].parentElement) {
      let borders = mutation.addedNodes[0].closest('.trick-image-preview').querySelectorAll('.border-isheader')
      // if the image is defined as favorite, change the header
      if (borders.length > 0) {
        document.getElementById('image-header-details').src = mutation.addedNodes[0].src
      }
    }
  }
});

let observerOptions = {
  childList: true,
}

// Checks for attribute changes
let imageHeaderObserver = new MutationObserver(function (mutations) {
  if (isProcessing) {
    // Ignore additional mutations while processing is in progress
    return;
  }

  isProcessing = true;

  // Retrieve only the last mutation in the list
  let lastMutation = mutations[mutations.length - 1];

  if (lastMutation.type === 'attributes' && lastMutation.attributeName === 'src') {
    // Processing the last mutation
    animateElement(headerImage, 'header-img-zoom-in', () => {
    });
  }

  // Reset isProcessing flag after 200 ms delay
  setTimeout(function () {
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

function addDeleteImageListener(deleteButton) {
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

      let imageLink = preview.querySelector('.favorite-image');
      // Add a link if none exists
      if (!imageLink) {
        imageLink = addLinkFavoriteImage(preview);
      }
      // Replaces existing image
      imageLink.replaceChild(img, preview.querySelector('.image-trick-details'));
      // No header set
      // Add borders and set this image to favorite
      if (borderIsHeader.length === 0) {
        preview.insertAdjacentHTML('afterbegin', bordersHtml);
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
    moveFavoriteImgBorders(preview);
  })
  previewObserver.observe(preview, observerOptions);
  previewObserver.observe(preview.querySelector('.favorite-image'), observerOptions);
  return link;
}

function reassignIsHeaderImage(tempDiv) {
  let otherFavoriteImages = document.querySelectorAll('.favorite-image');
  // Create an array with the NodeList
  let borders = Array.from(tempDiv.childNodes);
  if (otherFavoriteImages.length > 0) {
    // Retrieves the first valid preview
    let firstValidPreview = otherFavoriteImages[0].closest('.trick-image-preview');
    borders.forEach(border => {
      firstValidPreview.appendChild(border);
    })
  }
  // No image defined as header
  if (otherFavoriteImages.length === 0) {
    document.getElementById('image-header-details').src = imagePlaceholderSrc;
  }
}

function moveFavoriteImgBorders(preview) {
  let borderTop = document.getElementById('border-isheader-top');
  let borderRight = document.getElementById('border-isheader-right');
  let borderBot = document.getElementById('border-isheader-bot');
  let borderLeft = document.getElementById('border-isheader-left');
  // Select all borders
  animateBorder(borderTop, animationsBorders.top.fadeOut, 'fadeOut', preview);
  animateBorder(borderRight, animationsBorders.right.fadeOut, 'fadeOut', preview);
  animateBorder(borderBot, animationsBorders.left.fadeOut, 'fadeOut', preview);
  animateBorder(borderLeft, animationsBorders.bot.fadeOut, 'fadeOut', preview);
}

function animateBorder(border, animation, animationType, preview) {

  border.classList.add(animation);
  border.addEventListener('animationend', function afterFadeOutHandler(event) {
    if (animationType === 'fadeOut') {
      preview.appendChild(border);
    } else if (animationType === 'fadeIn') {
      // Set the image header
      let headerImage = document.getElementById('image-header-details');
      let imagePreview = preview.querySelector('.image-trick-details').src
      if (headerImage.src !== imagePreview) {
        headerImage.src = imagePreview
      }

    }

    border.classList.remove(animation);
    event.currentTarget.removeEventListener('animationend', afterFadeOutHandler);
  })
}

function animateElement(element, animation, actionAfterAnimation) {
  element.classList.add(animation)
  element.addEventListener('animationend', function animationEndHandler(event) {
    if (actionAfterAnimation !== 'undefined') {
      actionAfterAnimation();
    }
    element.classList.remove(animation);
    event.currentTarget.removeEventListener('animationend', animationEndHandler);
  })
}

function addImageForm(imagePlaceholder) {
  let newForm = createItemFromPrototype(imagePlaceholder, imagesCollection, addImageFormButton);

  const trickImgItem = document.getElementById(newForm.id);
  const newFileInput = trickImgItem.querySelector('.trick-form-file');
  // New delete button HTML
  let deleteButtonHtml = '<a role="button" class="delete-image-btn" id="delete-img-btn-' + imagesCollection.index + '">\n' +
    '<i class=\"fa-solid fa-trash icon-delete-img\"></i>\n' +
    '</a>'
  // Insert the new delete button
  newFileInput.parentElement.insertAdjacentHTML('beforeend', deleteButtonHtml)
  // Add listener to the delete button
  addDeleteImageListener(document.getElementById("delete-img-btn-" + imagesCollection.index))
  addInputFileValidation(newFileInput);
  addInputChangeListener(newFileInput, newForm.preview);
  // isTheHeader field set to 0 by default
  trickImgItem.querySelector('.trick-form-isheader').value = '0'
}

function addVideoForm(videoPlaceholder) {
  let newForm = createItemFromPrototype(videoPlaceholder, videoCollection, addVideoFormButton);
  let newFormElement = document.getElementById(newForm.id);
  addVideoLinkValidation(newFormElement.querySelector('.trick-video-link'));

  let tempDiv = document.createElement('div');
  tempDiv.classList.add('trick-video-actions');
  let videoLinkGroup = newFormElement.querySelector('.trick-video-link-group');
  let videoDeleteButtonHtml = '<a role="button" class="delete-video-btn" id="delete-video-btn-{{ loop.index-1 }}">\n' +
    '<i class="fa-solid fa-trash icon-delete-img"></i>\n' +
    '</a>'
  tempDiv.insertAdjacentHTML('afterbegin', videoDeleteButtonHtml);
  tempDiv.insertAdjacentElement('afterbegin', videoLinkGroup);
  newFormElement.insertAdjacentElement('beforeend', tempDiv);
  addDeleteVideoListener(newFormElement.querySelector('.delete-video-btn'));
  addVideoLinkKeyup(newFormElement.querySelector('.trick-video-link'))
}

function createItemFromPrototype(imagePlaceholder, collection, addItemFormButton) {
  // Retrieves the prototype of the collection field.
  let prototype = collection.data.dataset.prototype;

  let newForm = prototype.replace(/__name__/g, collection.index);
  // Increment imagesCollectionIndex for next addition.
  collection.index++;

  let tempDiv = document.createElement('div');
  tempDiv.innerHTML = newForm;
  tempDiv.firstElementChild.classList.add(collection.class);
  // Create new input preview
  let newPreview = document.createElement('div');
  newPreview.classList.add(collection.previewClass);
  newPreview.innerHTML = imagePlaceholder;

  // Retrieves new form id.
  let newFormId = tempDiv.firstChild.id;
  // Insert the new preview inside the tempDiv
  tempDiv.firstChild.insertBefore(newPreview, tempDiv.firstChild.firstChild)
  animateElement(tempDiv.firstChild, 'img-item-zoom-in', () => {
  })
  collection.data.insertBefore(tempDiv.firstChild, addItemFormButton);

  return {
    'id': newFormId,
    'preview': newPreview,
  };
}

function addVideoLinkValidation(link) {
  trickValidator
    .addField('#' + link.id, [
      {
        rule: 'required',
        errorMessage: 'The link cannot be empty',
      },
      {
        rule: 'customRegexp',
        value: /^https?:\/\/(?:www\.)?youtube\.com\/embed\/[A-Za-z0-9_-]{11}$/,
        errorMessage: 'The link is not valid.<br>https://www.youtube.com/embed/code',
      },
    ]);
}

function addVideoLinkKeyup(link) {
  link.addEventListener('keyup', () => {
    let preview = link.closest('.trick-video-item').querySelector('.trick-video-preview');
    if (link.classList.contains('just-validate-success-field')) {
      preview.innerHTML = '<iframe class="trick-video" src="' + link.value + '" allowfullscreen></iframe>'
    } else {
      preview.innerHTML = videoPlaceholder
    }
  })
}

function addDeleteVideoListener(button) {
  button.addEventListener('click', () => {
    let trickVideoItem = button.closest('.trick-video-item');
    animateElement(trickVideoItem, 'img-item-zoom-out', () => {
      trickVideoItem.remove()
    })
  })
}

addImageFormButton.addEventListener('click', function () {
  addImageForm(imagePlaceholder);
})

addVideoFormButton.addEventListener('click', function () {
  addVideoForm(videoPlaceholder);
})

fileInputs.forEach(fileInput => {
  //Retrieving the parent group with the image, preview and buttons
  const imageItem = fileInput.closest('.trick-image-item');
  const preview = imageItem.querySelector('.trick-image-preview');
  const linkFavoriteImg = preview.querySelector('.favorite-image');

  linkFavoriteImg.addEventListener('click', () => {
    moveFavoriteImgBorders(preview);
  })

  if (fileInput.classList.contains("isFilled") === false) {
    addInputFileValidation(fileInput);
  }
  //Preview image as it changes.
  addInputChangeListener(fileInput, preview);
  addDeleteImageListener(imageItem.querySelector('.delete-image-btn'));
  previewObserver.observe(preview, observerOptions);
  previewObserver.observe(preview.querySelector('.favorite-image'), observerOptions);
});

videosLinks.forEach(link => {
  addVideoLinkValidation(link);
  addVideoLinkKeyup(link);
})

document.querySelectorAll('.delete-video-btn').forEach(btn => {
  addDeleteVideoListener(btn)
})

trickValidator
  .addField('#trick_form_name', [
    {
      rule: 'maxLength',
      value: 50,
      errorMessage: 'The trick name must not exceed 50 characters.',
    },
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
});

imageHeaderObserver.observe(headerImage, {attributes: true, attributeFilter: ['src']})
addMobileMenuEvent();

