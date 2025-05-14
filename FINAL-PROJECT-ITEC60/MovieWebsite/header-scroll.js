window.onscroll = function() {
  var navbar = document.getElementById("navbar");
  if (window.pageYOffset > 50) {
    navbar.classList.add("scroll-header");
  } else {
    navbar.classList.remove("scroll-header");
  }
};