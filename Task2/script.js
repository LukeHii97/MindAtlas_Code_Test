function showForm() {
  var selection = document.querySelector(
    'input[name="selection"]:checked'
  ).value;
  var formContainer = document.getElementById("formContainer");

  // Clear the existing form
  formContainer.innerHTML = "";

  // Create the selected form dynamically
  if (selection === "user") {
    formContainer.innerHTML = `
          <form id="userForm" action="index.php" method="post" onsubmit="saveFormValues('user')">
              <label for="id">Student ID:</label>
              <input type="text" id="id" name="id" placeholder="Enter Student ID" /><br />

              <label for="username">Student Name:</label>
              <input type="text" id="username" name="username" placeholder="Enter Student Name" /><br />

              <input type="hidden" name="page" id="pageInput">

              <input type="submit" value="Submit" onclick="updatePage(1)" />
              <input type="reset" value="Reset" onclick="resetForm()" />
          </form>
      `;
    loadFormValues("user"); // Load the saved values for the user form
  } else if (selection === "course") {
    formContainer.innerHTML = `
          <form id="courseForm" action="index.php" method="post" onsubmit="saveFormValues('course')">
              <label for="course_id">Course ID:</label>
              <input type="text" id="course_id" name="course_id" placeholder="Enter Course ID" /><br />

              <label for="course_name">Course Name:</label>
              <input type="text" id="course_name" name="course_name" placeholder="Enter Course Name" /><br />

              <input type="hidden" name="page" id="pageInput">

              <input type="submit" value="Submit" onclick="updatePage(1)" />
              <input type="reset" value="Reset" onclick="resetForm()" />
          </form>
      `;
    loadFormValues("course"); // Load the saved values for the course form
  }

  // Save the selected option to localStorage
  localStorage.setItem("selectedForm", selection);
}

window.onload = function () {
  // Get the last selected option from localStorage
  var savedSelection = localStorage.getItem("selectedForm") || "user";
  document.querySelector(
    `input[name="selection"][value="${savedSelection}"]`
  ).checked = true;

  showForm(); // Show the form based on the saved selection
};

function resetForm() {
  var form = document.querySelector("form");

  if (form) {
    // Clear all text inputs
    var inputs = form.querySelectorAll('input[type="text"]');
    inputs.forEach(function (input) {
      input.value = ""; // Clear the input value
    });

    // Optionally, reset hidden fields if needed
    document.getElementById("pageInput").value = 1;

    // Clear saved values in localStorage
    localStorage.removeItem(`${form.id}FormValues`);

    // Ensure form is also reset
    form.reset();
  }
}

function updatePage(page) {
  document.getElementById("pageInput").value = page;
  document.querySelector("form").submit();
  document.getElementById("pageInput").value = 1; // Reset to page 1
}

function goToPage() {
  var customPageInput = document.getElementById("customPageInput").value;
  var totalPages = parseInt(document.getElementById("customPageInput").max);
  var page = Math.max(1, Math.min(totalPages, parseInt(customPageInput)));

  updatePage(page);
}

function saveFormValues(formType) {
  var formValues = {};
  var inputs = document.querySelectorAll(`#${formType}Form input[type="text"]`);
  inputs.forEach(function (input) {
    formValues[input.id] = input.value;
  });

  localStorage.setItem(`${formType}FormValues`, JSON.stringify(formValues));
}

function loadFormValues(formType) {
  var savedValues = localStorage.getItem(`${formType}FormValues`);
  if (savedValues) {
    var formValues = JSON.parse(savedValues);
    for (var key in formValues) {
      if (formValues.hasOwnProperty(key)) {
        document.getElementById(key).value = formValues[key];
      }
    }
  }
}
