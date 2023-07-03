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
    alertBox.addEventListener('click', (event) => {
      removeAlertBox();
    })
    setTimeout(removeAlertBox, 2000);
  })
}
