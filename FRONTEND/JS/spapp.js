/* ===================================
   SPAPP.JS - SPA Router (FROM LAB)
=================================== */

$(document).ready(function() {
  
  function loadPage(page) {
    const section = $("#" + page);
    $("#spapp section").hide(); // hide all
    section.show(); // show only active section
    
    // Load content from HTML folder
    section.load(`HTML/${page}.html`, function(response, status) {
      if (status === "error") {
        section.html(`<div class="alert alert-danger mt-3">Page ${page}.html not found.</div>`);
      } else {
        // After page loads, initialize its service if exists
        initializePageService(page);
      }
    });
  }
  
  function initializePageService(page) {
    // Call init() for each page's service
    switch(page) {
      case 'products':
        if (typeof ProductService !== 'undefined' && ProductService.init) {
          ProductService.init();
        }
        break;
      case 'cart':
        if (typeof CartService !== 'undefined' && CartService.init) {
          CartService.init();
        }
        break;
      case 'wishlist':
        if (typeof WishlistService !== 'undefined' && WishlistService.init) {
          WishlistService.init();
        }
        break;
      case 'checkout':
        if (typeof CheckoutService !== 'undefined' && CheckoutService.init) {
          CheckoutService.init();
        }
        break;
      case 'user':
        if (typeof UserService !== 'undefined' && UserService.init) {
          UserService.init();
        }
        break;
      case 'admin':
        if (typeof AdminService !== 'undefined' && AdminService.init) {
          AdminService.init();
        }
        break;
    }
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