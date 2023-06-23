window.addEventListener("load", function () {
  let textElement = document.getElementById("header-title");
  textElement.classList.add("animated-text");
});

function reloadTricks() {
  document.getElementById("btn-load-more-tricks").addEventListener("click", () => {
    // Current number of loaded Tricks
    const nbLoadedTricks = document.getElementById("trick-section-home").childElementCount;
    let trickList = document.getElementById("trick-list");
    addXmlhttpRequest("tricks/loadMore/" + nbLoadedTricks, trickList, function scrollToTricksEnd () {
      let newTrickList = document.getElementById("trick-list");

      // Scroll to the end of the section
      let position = newTrickList.offsetTop + newTrickList.offsetHeight;
      window.scrollTo(0, position);
      addDeleteListener();

      if (document.getElementById("btn-load-more-tricks")) {
        reloadTricks();
      }
    })
  })
}

function addDeleteListener() {
  document.querySelectorAll('.trick-delete-button').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('trick-to-delete').innerHTML = btn.closest('.trick-name');
    })
  })
}

document.getElementById('delete-trick-btn').addEventListener('click', () => {
  const xmlHttp = new XMLHttpRequest();
  xmlHttp.onreadystatechange = function () {
    if (this.readyState === 4 && this.status === 200) {
      const newContent = this.responseText;
    }
  }
  xmlHttp.open("GET", "trick/delete/", true);
  xmlHttp.send();
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


