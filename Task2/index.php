<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Author" content="Luke Hii">
    <title>MindAtlas Code Test</title>
    <link rel="stylesheet" href="styles.css"/>
    <!-- <script src="script.js"></script> -->
    <script>
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

    </script>

</head>
<body>
    <h1>This site is for MindAtlas Code Test - Luke</h1>

    <div>
        <label><input type="radio" name="selection" checked="checked" value="user" onclick="showForm()"> User</label>
        <label><input type="radio" name="selection" value="course" onclick="showForm()"> Course</label>
    </div>

    <!-- Container where forms will be dynamically generated -->
    <div id="formContainer"></div>

    <div id="noData" style="display: none;">
        No data found
    </div>

  <?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

class TableRows extends RecursiveIteratorIterator {
    function __construct($it) {
        parent::__construct($it, self::LEAVES_ONLY);
    }

    function current() {
        return "<td>" . parent::current() . "</td>";
    }

    function beginChildren() {
        echo "<tr>";
    }

    function endChildren() {
        echo "</tr>" . "\n";
    }
}

// Database connection details
$servername = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Number of records per page
    $recordsPerPage = 5;

    // Determine the current page number
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $start = ($page - 1) * $recordsPerPage;

    // Check if a user ID or course ID is submitted
    if (!empty($_POST["id"])) {
        $userid = $_POST["id"];
        getUserInformationById($conn, $userid, $start, $recordsPerPage, $page);
    }
    elseif (!empty($_POST["username"])) {
        $studentname = $_POST["username"];
        getUserInformationByName($conn, $studentname, $start, $recordsPerPage, $page);
    }
    elseif (!empty($_POST["course_id"])) {
        $courseid = $_POST["course_id"];
        getCourseInformationById($conn, $courseid, $start, $recordsPerPage, $page);
    }
    elseif (!empty($_POST["course_name"])) {
        $coursename = $_POST["course_name"];
        getCourseInformationByName($conn, $coursename, $start, $recordsPerPage, $page);
    }
    else {
        echo "<p>Please select an option and enter an ID.</p>";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;

function checkResults($stmt) {
    if ($stmt->rowCount() == 0) {
        echo "<script>document.getElementById('noData').style.display = 'block';</script>";
        return false;
    }
    return true;
}

function getUserInformationById($conn, $userid, $start, $recordsPerPage, $currentPage) {
    $stmt = $conn->prepare("SELECT id, firstname, surname FROM users WHERE id = :userid");
    $stmt->bindParam(':userid', $userid);
    $stmt->execute();

    if (!checkResults($stmt)) {
        return;
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<h2>User Information</h2>";
    echo "<table>";
    echo "<tr><th>ID</th><th>First Name</th><th>Surname</th></tr>";
    echo "<tr><td>{$user['id']}</td><td>{$user['firstname']}</td><td>{$user['surname']}</td></tr>";
    echo "</table>";

    getUserEnrollmentByUserId($conn, $userid, $start, $recordsPerPage, $currentPage);
}

function getUserInformationByName($conn, $studentname, $start, $recordsPerPage, $currentPage) {
    $parts = explode(" ", $studentname);
    $surname = array_pop($parts);
    $firstname = implode(" ", $parts);
    
    $stmt = $conn->prepare("SELECT id, firstname, surname FROM users WHERE firstname = :firstname AND surname = :surname");
    $stmt->bindParam(':firstname', $firstname);
    $stmt->bindParam(':surname', $surname);
    $stmt->execute();

    if (!checkResults($stmt)) {
        return;
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<h2>User Information</h2>";
    echo "<table>";
    echo "<tr><th>ID</th><th>First Name</th><th>Surname</th></tr>";
    echo "<tr><td>{$user['id']}</td><td>{$user['firstname']}</td><td>{$user['surname']}</td></tr>";
    echo "</table>";

    getUserEnrollmentByUserId($conn, $user['id'], $start, $recordsPerPage, $currentPage);
}

function getUserEnrollmentByUserId($conn, $userid, $start, $recordsPerPage, $currentPage) {
    $stmt = $conn->prepare("SELECT c.ID, c.Description, e.CompletionStatus FROM enrollments AS e 
                                LEFT JOIN courses AS c ON e.CourseID = c.ID 
                                WHERE e.UserID = :userid 
                                LIMIT $start, $recordsPerPage");
    $stmt->bindParam(':userid', $userid);
    $stmt->execute();

    if (!checkResults($stmt)) {
        return;
    }

    echo "<h2>Enrollment Information</h2>";
    echo "<table>";
    echo "<tr><th>Course ID</th><th>Description</th><th>Completion Status</th></tr>";
    foreach (new TableRows(new RecursiveArrayIterator($stmt->fetchAll(PDO::FETCH_ASSOC))) as $k => $v) {
        echo $v;
    }
    echo "</table>";

    // Fetch total count for pagination
    $totalCount = getTotalCount($conn, "SELECT COUNT(*) FROM enrollments WHERE UserID = :userid", ":userid", $userid);
    $totalPages = ceil($totalCount / $recordsPerPage);

    // Pagination controls
    if ($totalPages > 1) {
        echo '<div class="pagination">';
        
        // Display "First Page"
        if ($currentPage > 1) {
            echo '<a href="javascript:void(0);" onclick="updatePage(1)">First</a>';
        }

        // Display range of page numbers with ellipses
        if ($currentPage > 4) {
            echo '<a href="javascript:void(0);" onclick="updatePage('.($currentPage - 1).')">Previous</a>...';
        }

        // Display page numbers around the current page
        $startPage = max(1, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);

        for ($i = $startPage; $i <= $endPage; $i++) {
            echo '<a href="javascript:void(0);" onclick="updatePage('.$i.')" class="' . ($i == $currentPage ? 'current' : '') . '">' . $i . '</a>';
        }

        // Display ellipses if needed
        if ($currentPage < $totalPages - 3) {
            echo '...<a href="javascript:void(0);" onclick="updatePage('.($currentPage + 1).')">Next</a>';
        }

        // Display "Last Page"
        if ($currentPage < $totalPages) {
            echo '<a href="javascript:void(0);" onclick="updatePage('.$totalPages.')">Last</a>';
        }

        // Add custom page input and button
        echo '<div class="custom-page">
                <label for="customPageInput">Go to page:</label>
                <input type="number" id="customPageInput" min="1" max="'.$totalPages.'" value="'.$currentPage.'">
                /'.$totalPages.'
                <button onclick="goToPage()">Go</button>
            </div>';
        
        echo '</div>';
    }
}

function getCourseInformationById($conn, $courseid, $start, $recordsPerPage, $currentPage) {
    $stmt = $conn->prepare("SELECT id, description FROM courses WHERE id = :courseid");
    $stmt->bindParam(':courseid', $courseid);
    $stmt->execute();

    if (!checkResults($stmt)) {
        return;
    }

    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<h2>Course Information</h2>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Description</th></tr>";
    echo "<tr><td>{$course['id']}</td><td>{$course['description']}</td></tr>";
    echo "</table>";

     getUserEnrollmentByCourseId($conn, $courseid, $start, $recordsPerPage, $currentPage);
}

function getCourseInformationByName($conn, $coursename, $start, $recordsPerPage, $currentPage) {
    $stmt = $conn->prepare("SELECT id, description FROM courses WHERE description = :coursename");
    $stmt->bindParam(':coursename', $coursename);
    $stmt->execute();

    if (!checkResults($stmt)) {
        return;
    }

    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<h2>Course Information</h2>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Description</th></tr>";
    echo "<tr><td>{$course['id']}</td><td>{$course['description']}</td></tr>";
    echo "</table>";

     getUserEnrollmentByCourseId($conn, $course['id'], $start, $recordsPerPage, $currentPage);
}

function getUserEnrollmentByCourseId($conn, $courseid, $start, $recordsPerPage, $currentPage) {
    $stmt = $conn->prepare("SELECT u.id, u.firstname, u.surname, e.CompletionStatus FROM enrollments AS e 
                              LEFT JOIN users AS u ON e.UserID = u.ID 
                              WHERE e.CourseID = :courseid 
                              LIMIT $start, $recordsPerPage");
    $stmt->bindParam(':courseid', $courseid);
    $stmt->execute();

    if (!checkResults($stmt)) {
        return;
    }

    echo "<h2>Enrolled Students</h2>";
    echo "<table>";
    echo "<tr><th>User ID</th><th>First Name</th><th>Surname</th><th>Completion Status</th></tr>";
    foreach (new TableRows(new RecursiveArrayIterator($stmt->fetchAll(PDO::FETCH_ASSOC))) as $k => $v) {
        echo $v;
    }
    echo "</table>";

    // Fetch total count for pagination
    $totalCount = getTotalCount($conn, "SELECT COUNT(*) FROM enrollments WHERE CourseID = :courseid", ":courseid" ,$courseid);
    $totalPages = ceil($totalCount / $recordsPerPage);

    // Pagination controls
    if ($totalPages > 1) {
        echo '<div class="pagination">';
        
        // Display "First Page"
        if ($currentPage > 1) {
            echo '<a href="javascript:void(0);" onclick="updatePage(1)">First</a>';
        }

        // Display range of page numbers with ellipses
        if ($currentPage > 4) {
            echo '<a href="javascript:void(0);" onclick="updatePage('.($currentPage - 1).')">Previous</a>...';
        }

        // Display page numbers around the current page
        $startPage = max(1, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);

        for ($i = $startPage; $i <= $endPage; $i++) {
            echo '<a href="javascript:void(0);" onclick="updatePage('.$i.')" class="' . ($i == $currentPage ? 'current' : '') . '">' . $i . '</a>';
        }

        // Display ellipses if needed
        if ($currentPage < $totalPages - 3) {
            echo '...<a href="javascript:void(0);" onclick="updatePage('.($currentPage + 1).')">Next</a>';
        }

        // Display "Last Page"
        if ($currentPage < $totalPages) {
            echo '<a href="javascript:void(0);" onclick="updatePage('.$totalPages.')">Last</a>';
        }

        // Add custom page input and button
        echo '<div class="custom-page">
                <label for="customPageInput">Go to page:</label>
                <input type="number" id="customPageInput" min="1" max="'.$totalPages.'" value="'.$currentPage.'">
                /'.$totalPages.'
                <button onclick="goToPage()">Go</button>
            </div>';
        
        echo '</div>';
    }
}

function getTotalCount($conn, $query, $bindword, $param) {
    $stmt = $conn->prepare($query);
    $stmt->bindParam($bindword, $param); // Ensure this matches the placeholder
    $stmt->execute();
    return $stmt->fetchColumn();
}
?>

</body>
</html>
