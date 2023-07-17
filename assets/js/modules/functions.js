export function addMobileMenuEvent() {
  let toggleMenu = document.querySelector('.menu-toggle');
  toggleMenu.addEventListener('click', () => {
    let headList = document.querySelector('.head-list')
    headList.classList.toggle('show-list');
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
  document.querySelectorAll('.alert-box').forEach(alertBox => {
    let removeAlertBox = () => {
      alertBox.classList.remove('alert-fade-in');
      alertBox.classList.add('alert-fade-out');
      alertBox.addEventListener('animationend', () => {
        alertBox.remove()
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
