function validateSignupForm() {
  const firstName = document.getElementById("first_name").value.trim();
  const lastName = document.getElementById("last_name").value.trim();
  const email = document.getElementById("email").value.trim();
  const mobile = document.getElementById("mobile").value.trim();
  const password = document.getElementById("password").value.trim();
  const confirmPassword = document
    .getElementById("confirm_password")
    .value.trim();
  const role = document.getElementById("role").value;
  const userImage = document.getElementById("user_image").files[0];
  let errors = [];

  if (
    !firstName ||
    !lastName ||
    !email ||
    !mobile ||
    !password ||
    !confirmPassword ||
    !role ||
    !userImage
  ) {
    errors.push("All fields are required.");
  }

  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailPattern.test(email)) {
    errors.push("Invalid email format.");
  }

  const mobilePattern = /^\d{10}$/;
  if (!mobilePattern.test(mobile)) {
    errors.push("Mobile number must be 10 digits.");
  }

  const passwordPattern =
    /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
  if (!passwordPattern.test(password)) {
    errors.push(
      "Password must be at least 8 characters long and include uppercase, lowercase, numbers, and special characters."
    );
  }

  if (password !== confirmPassword) {
    errors.push("Passwords do not match.");
  }

  const allowedImageExtensions = ["jpg", "jpeg", "png"];
  const fileExtension = userImage.name.split(".").pop().toLowerCase();
  if (!allowedImageExtensions.includes(fileExtension)) {
    errors.push(
      "Upload failed. Allowed file types: " + allowedImageExtensions.join(", ")
    );
  }

  if (errors.length > 0) {
    alert(errors.join("\n"));
    return false;
  }

  return true;
}
