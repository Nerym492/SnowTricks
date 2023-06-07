window.addEventListener("load", function() {
  let textElement = document.getElementById("header-title");
  textElement.classList.add("animated-text");
});

function reloadTricks (){
  document.getElementById("btn-load-more-tricks").addEventListener("click", ()=> {
    const xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
        const newContent = this.responseText;
        const trickList = document.getElementById("trick-list");
        const tempElement = document.createElement('div');
        tempElement.innerHTML = newContent;
        trickList.replaceWith(tempElement.firstChild);

        let newTrickList = document.getElementById("trick-list");

        // Scroll to the end of the section
        let position = newTrickList.offsetTop + newTrickList.offsetHeight;
        window.scrollTo(0, position);

        if (document.getElementById("btn-load-more-tricks")) {
          reloadTricks();
        }
      }
    }
    // Current number of loaded Tricks
    const nbLoadedTricks = document.getElementById("trick-section-home").childElementCount;
    xmlHttp.open("GET", "tricks/loadMore/"+nbLoadedTricks,true);
    xmlHttp.send();
  })
}

reloadTricks();


