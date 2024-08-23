document.addEventListener('DOMContentLoaded', function() {
  const navButton = document.getElementById('nav_button');
  const navMenu = document.getElementById('nav_menu');
  let isOpen = false;

  navButton.addEventListener('click', function() {
    isOpen = !isOpen;
    if (isOpen) {
      navMenu.style.left = '-20%';
      navButton.style.transform = 'translateX(calc(335% - 90px))';
    } else {
      navMenu.style.left = '-100%';
      navButton.style.transform = 'none';
    }
  });
});

document.addEventListener("DOMContentLoaded", function() {
  document.getElementById("log_in").addEventListener("click", function() {
      window.location = "../works/log_in_form.html";
  });

  document.getElementById("sign_up").addEventListener("click", function() {
      window.location.href = "../works/sign_up_form.html"; 
  });
});