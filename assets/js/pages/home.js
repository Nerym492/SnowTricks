window.addEventListener("load", function () {
  let textElement = document.getElementById("header-title");
  textElement.classList.add("animated-text");
});

function reloadTricks() {
  document.getElementById("btn-load-more-tricks").addEventListener("click", () => {
    // Current number of loaded Tricks
    const nbLoadedTricks = document.getElementById("trick-section-home").childElementCount;
    let trickList = document.getElementById("trick-list");
    addXmlhttpRequest("tricks/loadMore/" + nbLoadedTricks, trickList, scrollToTricksEnd)
  })
}

function scrollToTricksEnd () {
  let newTrickList = document.getElementById("trick-list");

  // Scroll to the end of the section
  let position = newTrickList.offsetTop + newTrickList.offsetHeight;
  window.scrollTo(0, position);
  addDeleteListener();

  if (document.getElementById("btn-load-more-tricks")) {
    reloadTricks();
  }
}

function addDeleteListener() {
  document.querySelectorAll('.trick-delete-button').forEach(btn => {
    btn.addEventListener('click', () => {
      let trickName = btn.closest('.trick-description').querySelector('.trick-name').innerHTML
      document.getElementById('trick-to-delete').innerHTML = trickName;
    })
  })
}

document.getElementById('delete-trick-btn').addEventListener('click', (event) => {
    const trickName = event.target.firstElementChild.innerHTML
    const nbLoadedTricks = document.getElementById("trick-section-home").childElementCount;
    let trickList = document.getElementById("trick-list");
    addXmlhttpRequest('tricks/delete/'+trickName+'/loaded/'+nbLoadedTricks, trickList, scrollToTricksEnd)
})

function addXmlhttpRequest(url, elementToRefresh, afterRefreshAction) {
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
  xmlHttp.open("GET", url, true);
  xmlHttp.send();
}

reloadTricks();
addDeleteListener();


