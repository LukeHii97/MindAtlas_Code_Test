// Function to show or hide forms based on selected radio button
function showForm() {
  var selection = document.querySelector(
    'input[name="selection"]:checked'
  ).value;
  var userForm = document.getElementById("userForm");
  var courseForm = document.getElementById("courseForm");

  // Hide both forms initially
  userForm.style.display = "none";
  courseForm.style.display = "none";

  // Show the appropriate form based on the selection
  if (selection == "user") {
    userForm.style.display = "block";
  } else if (selection == "course") {
    courseForm.style.display = "block";
  }
}

// Show the appropriate form on page load
window.onload = function () {
  showForm(); // Show the user form by default on page load
};

// Function to reset the form and reload the page
function resetForm() {
  window.location.href = "index.php";
}
