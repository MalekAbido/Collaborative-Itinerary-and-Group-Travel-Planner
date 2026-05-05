function toggleVisibility(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon = document.getElementById(iconId);

  if (input.type === "password") {
    input.type = "text";
    icon.textContent = "visibility";
  } else {
    input.type = "password";
    icon.textContent = "visibility_off";
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const registerForm = document.getElementById("register-form");
  const loginForm = document.getElementById("login-form");

  const notyf = new Notyf({
    duration: 4000,
    position: { x: "right", y: "bottom" },
    dismissible: true,
  });

  async function handleFormSubmit(event, form, url, successMessage) {
    event.preventDefault();
    notyf.dismissAll();

    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML =
      'Processing... <span class="material-symbols-outlined animate-spin text-[20px]">autorenew</span>';

    const formData = new FormData(form);

    try {
      const response = await fetch(url, {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        notyf.success(successMessage);
        setTimeout(() => {
          window.location.href = result.redirect;
        }, 1500);
      } else {
        result.errors.forEach((error) => {
          notyf.error(error);
        });
      }
    } catch (error) {
      notyf.error("A network error occurred. Please try again.");
      console.error(error);
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnText;
    }
  }

  if (registerForm) {
    registerForm.addEventListener("submit", function (event) {
      handleFormSubmit(
        event,
        registerForm,
        "/register/process",
        "Account created successfully! Redirecting...",
      );
    });
  }

  if (loginForm) {
    loginForm.addEventListener("submit", function (event) {
      handleFormSubmit(event, loginForm, "/login/process", "Login successful!");
    });
  }
});
