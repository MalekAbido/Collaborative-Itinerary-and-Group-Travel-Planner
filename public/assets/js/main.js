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
  const form = document.getElementById("register-form");

  const notyf = new Notyf({
    duration: 3000,
    position: { x: "right", y: "bottom" },
    dismissible: true,
  });

  if (form) {
    form.addEventListener("submit", async function (event) {
      event.preventDefault();

      const formData = new FormData(form);

      try {
        const response = await fetch("/register/process", {
          method: "POST",
          body: formData,
        });

        const result = await response.json();

        if (result.success) {
          notyf.success("Account created successfully! Redirecting...");

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
      }
    });
  }
});
