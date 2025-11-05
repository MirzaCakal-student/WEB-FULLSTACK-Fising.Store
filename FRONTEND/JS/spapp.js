$(document).ready(function() {

  function loadPage(page) {
    const section = $("#" + page);
    $("#spapp section").hide(); // hide all
    section.show(); // show only active section

    // Try to load .php file instead of .html
    section.load(`HTML/${page}.php`, function(response, status) {
      if (status === "error") {
        section.html(`<div class="alert alert-danger mt-3">⚠️ Page <b>${page}.php</b> not found.</div>`);
      }
    });
  }

  function getHash() {
    return window.location.hash.replace("#", "") || "home";
  }

  // Initial load
  loadPage(getHash());

  // On hash change
  $(window).on("hashchange", function() {
    loadPage(getHash());
  });
});
