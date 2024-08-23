function togglePasswordVisibility(id) {
  const passwordField = document.getElementById(id);
  const eyeIcon = document.getElementById(`eye-icon-${id}`);
  const isPassword = passwordField.type === "password";

  passwordField.type = isPassword ? "text" : "password";

  if (isPassword) {
    eyeIcon.classList.remove("fa-eye");
    eyeIcon.classList.add("fa-eye-slash");
  } else {
    eyeIcon.classList.remove("fa-eye-slash");
    eyeIcon.classList.add("fa-eye");
  }
}

function validateForm(event) {
  event.preventDefault();
  const form = document.querySelector("form");
  const inputs = form.querySelectorAll("input, select");
  let isValid = true;

  form.querySelectorAll(".validation-message").forEach((msg) => msg.remove());

  inputs.forEach((input) => {
    if (input.value.trim() === "") {
      isValid = false;
      const message = document.createElement("div");
      message.className = "validation-message";
      message.textContent = `${input.previousElementSibling.textContent} 
        is required.`;
      input.parentNode.appendChild(message);
    }
  });

  const password = document.getElementById("password");
  const confirmPassword = document.getElementById("confirm-password");
  if (password.value !== confirmPassword.value) {
    isValid = false;
    const message = document.createElement("div");
    message.className = "validation-message";
    message.textContent = "Passwords do not match.";
    confirmPassword.parentNode.appendChild(message);
  }

  if (isValid) {
    alert("Form is valid! Submitting...");
  }
}

document
  .querySelector(".sign-in-button")
  .addEventListener("click", validateForm);
