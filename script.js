document.addEventListener("DOMContentLoaded", () => {
  // 1. Form Validation (login.html)
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const email = document.getElementById("email");
      const password = document.getElementById("password");
      const emailError = document.getElementById("emailError");
      const passwordError = document.getElementById("passwordError");
      let isValid = true;

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!email.value.trim() || !emailRegex.test(email.value.trim())) {
        email.classList.add("is-invalid");
        if (emailError) {
          emailError.textContent = "Please enter a valid email address.";
          emailError.style.display = "block";
        }
        isValid = false;
      } else {
        email.classList.remove("is-invalid");
        if (emailError) emailError.style.display = "none";
      }

      if (!password.value.trim() || password.value.trim().length < 6) {
        password.classList.add("is-invalid");
        if (passwordError) {
          passwordError.textContent = "Password must be at least 6 characters.";
          passwordError.style.display = "block";
        }
        isValid = false;
      } else {
        password.classList.remove("is-invalid");
        if (passwordError) passwordError.style.display = "none";
      }

      if (isValid) {
        alert("Login successful!");
        loginForm.reset();
      }
    });
  }

  // 2. Smooth Scrolling for Anchor Links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: "smooth" });
      }
    });
  });
});