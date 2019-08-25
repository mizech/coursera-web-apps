document.getElementById("login-form").addEventListener("submit", () => {
  const email = document.getElementById("email").value;
  const pass = document.getElementById("pass").value;

  if (
    email === null ||
    email.length === 0 ||
    (pass === null || pass.length === 0)
  ) {
    alert("Both fields must be filled out");
    return false;
  } else if (!email.includes("@")) {
    alert("Invalid email address");
    return false;
  }

  return true;
});
