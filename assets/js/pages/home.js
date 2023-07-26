import {
  addXmlhttpRequest,
  addAlertListener,
  addMobileMenuEvent,
  resizeHeaderHeight,
} from "../modules/functions.js";

window.addEventListener("load", function () {
  let textElement = document.getElementById("header-title");
  textElement.classList.add("animated-text");
});

function reloadTricks() {
  document
    .getElementById("btn-load-more-tricks")
    .addEventListener("click", () => {
      // Current number of loaded Tricks
      const nbLoadedTricks =
        document.getElementById("trick-section-home").childElementCount;
      let trickList = document.getElementById("trick-list");
      addXmlhttpRequest(
        "GET",
        "tricks/loadMore/" + nbLoadedTricks,
        null,
        trickList,
        () => {
          addDeleteListener();
          addAlertListener();
          if (document.getElementById("btn-load-more-tricks")) {
            reloadTricks();
          }
        },
      );
    });
}

function addDeleteListener() {
  document.querySelectorAll(".trick-delete-button").forEach((btn) => {
    btn.addEventListener("click", () => {
      let trickName = btn
        .closest(".trick-description")
        .querySelector(".trick-name").innerHTML;
      document.getElementById("trick-to-delete").innerHTML = trickName;
    });
  });
}

document
  .getElementById("delete-trick-btn")
  .addEventListener("click", (event) => {
    const trickName = event.target.firstElementChild.innerHTML;
    const nbLoadedTricks =
      document.getElementById("trick-section-home").childElementCount;
    let trickList = document.getElementById("trick-list");
    addXmlhttpRequest(
      "GET",
      "tricks/delete/" + trickName + "/loaded/" + nbLoadedTricks,
      null,
      trickList,
      () => {
        addDeleteListener();
        addAlertListener();
        document.getElementById("trick-list").scrollIntoView();
        if (document.getElementById("btn-load-more-tricks")) {
          reloadTricks();
        }
      },
    );
  });

reloadTricks();
addMobileMenuEvent();
resizeHeaderHeight();
addDeleteListener();
addAlertListener();
