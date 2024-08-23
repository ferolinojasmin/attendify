document.addEventListener("DOMContentLoaded", function () {
  const logoutButton = document.getElementById("logout");
  if (logoutButton) {
      logoutButton.addEventListener("click", function () {
          window.location.href = "../index.html";
      });
  }
});
