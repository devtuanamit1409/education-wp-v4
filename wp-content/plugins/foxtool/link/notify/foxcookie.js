document.addEventListener("DOMContentLoaded", function () {
  const ftcookiebg = document.getElementById("ft-cookie");
  if (!ftcookiebg) return;
  const closeButtons = document.querySelectorAll(".ft-cookie-oke, .ft-cookie-close");
  if (!localStorage.getItem("ftcookie")) {
	ftcookiebg.style.display = "block";
  }
  closeButtons.forEach(button => {
	button.addEventListener("click", function () {
	  localStorage.setItem("ftcookie", "true");
	  ftcookiebg.style.display = "none";
	});
  });
});