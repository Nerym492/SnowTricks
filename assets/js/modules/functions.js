export function resizeHeaderHeight() {
  let newHeaderHight = window.visualViewport.height - document.querySelector('.head-nav').offsetHeight;
  document.querySelector('.masthead').style.height = newHeaderHight + 'px';
}

export function addMobileMenuEvent() {
  let toggleMenu = document.querySelector('.menu-toggle');
  toggleMenu.addEventListener('click', () => {
    let headList = document.querySelector('.head-list')
    let profilePictureGroup = document.querySelector('.profile-picture-group')
    let menuNavbar = document.querySelector('.menu-navbar')

    headList.classList.toggle('show-list');


    menuNavbar.addEventListener('transitionstart', () => {
      if (headList.classList.contains('show-list') && profilePictureGroup) {
        profilePictureGroup.style.display = 'flex';
        // profilePictureGroup.classList.remove('profile-picture-fade-out');
        // profilePictureGroup.classList.add('profile-picture-fade-in')
      } else if (!headList.classList.contains('show-list') && profilePictureGroup) {
        profilePictureGroup.style.display = '';
      }
    }, {once: true})
  })
}

export function addXmlhttpRequest(method, url, formData, elementToRefresh, afterRefreshAction) {
  const xmlHttp = new XMLHttpRequest();
  xmlHttp.onreadystatechange = function () {
    if (this.readyState === 4 && this.status === 200) {
      let newContent = this.responseText;
      let tempElement = document.createElement('div');
      tempElement.innerHTML = newContent;
      elementToRefresh.replaceWith(tempElement.firstChild);

      afterRefreshAction();
    }
  }
  xmlHttp.open(method, url, true);
  xmlHttp.send(formData);
}

export function addAlertListener() {
  let firstAlerbox = document.querySelector('.alert-box-container')
  if (firstAlerbox) {
    window.scrollTo(0, firstAlerbox.getBoundingClientRect().top + window.scrollY)
  }
  document.querySelectorAll('.alert-box').forEach(alertBox => {
    let removeAlertBox = () => {
      alertBox.classList.remove('alert-fade-in');
      alertBox.classList.add('alert-fade-out');
      alertBox.addEventListener('animationend', () => {
        alertBox.closest('.alert-box-container').remove()
      })
    }
    alertBox.addEventListener('click', () => {
      removeAlertBox();
    })
    setTimeout(removeAlertBox, 3000);
  })
}

export function addFileValidationRules() {
  return {
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
  }
}
