const togglePassword = document.querySelector("#togglePassword");
const password = document.querySelector("#password");
const loginForm = document.querySelector("#loginForm");

togglePassword.addEventListener("click", function () {
  const type =
    password.getAttribute("type") === "password" ? "text" : "password";
  password.setAttribute("type", type);
  this.classList.toggle("fa-eye-slash");
});

loginForm.addEventListener("submit", function (event) {
  event.preventDefault();

  const specialKey = document
    .getElementById("username")
    .value.trim()
    .toUpperCase();
  const password = document.getElementById("password").value.trim();

  if (specialKey === "" || password === "") {
    alert("Please fill in all fields.");
    return;
  }

  // Set the correct action URL for the form based on the special key
  let actionUrl;
  if (
    specialKey.startsWith("S") ||
    specialKey.startsWith("P") ||
    specialKey.startsWith("A")
  ) {
    actionUrl = "../php/login.php"; // Use the correct login script
  } else {
    alert("Invalid Special Key.");
    return;
  }

  // Set the form's action to the correct URL and submit it
  loginForm.action = actionUrl;
  loginForm.method = "POST"; // Ensure the method is POST
  loginForm.submit(); // Submit the form to the action URL
});
