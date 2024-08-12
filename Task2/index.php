<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Author" content="Luke Hii">
    <title>MindAtlas Code Test</title>
     <!-- External CSS file for styling -->
    <link rel="stylesheet" href="styles.css">
    <!-- External JavaScript file -->
    <script src="script.js" defer></script>
</head>
<body>
    <h1>This site is for MindAtlas Code Test - Luke</h1>

    <!-- Radio buttons to select between User and Course forms -->
    <div>
        <label><input type="radio" name="selection" value="user" checked onclick="showForm()"> User</label>
        <label><input type="radio" name="selection" value="course" onclick="showForm()"> Course</label>
    </div>

    <!-- User form -->
    <form id="userForm" action="index.php" method="get">
        <label for="id">Student ID:</label>
        <input type="text" id="id" name="id" placeholder="Enter Student ID" /><br />
        <input type="submit" value="Submit" />
        <input type="reset" value="Reset" onclick="resetForm()" />
    </form>

    <!-- Course form -->
    <form id="courseForm" action="index.php" method="get">
        <label for="course_id">Course ID:</label>
        <input type="text" id="course_id" name="course_id" placeholder="Enter Course ID" /><br />
        <input type="submit" value="Submit" />
        <input type="reset" value="Reset" onclick="resetForm()" />
    </form>

    <!-- Div to show when no data is found -->
    <div id="noData">
        No data found
    </div>

    <?php
    // PHP code for handling database connection and displaying results

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
    $servername = "yourServerName";
    $username = "yourUserName";
    $password = "yourPassword";
    $dbname = "yourDatabaseName";

    try {
        // Create a new PDO instance
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if a user ID or course ID is submitted
        if (!empty($_GET["id"])) {
            $userid = $_GET["id"];
            getUserInformation($conn, $userid);
        } elseif (!empty($_GET["course_id"])) {
            $courseid = $_GET["course_id"];
            getCourseInformation($conn, $courseid);
        } else {
            echo "<p>Please select an option and enter an ID.</p>";
        }

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    $conn = null;

    // Function to check if results are returned
    function checkResults($stmt) {
        if ($stmt->rowCount() == 0) {
            echo "<script>document.getElementById('noData').style.display = 'block';</script>";
            return false;
        }
        return true;
    }

    // Function to get user information
    function getUserInformation($conn, $userid) {
        $stmt = $conn->prepare("SELECT id, firstname, surname FROM users WHERE id = :userid");
        $stmt->bindParam(':userid', $userid);
        $stmt->execute();

        if (!checkResults($stmt)) {
            return;
        }

        echo "<table>";
        echo "<tr><th>ID</th><th>First Name</th><th>Last Name</th></tr>";
        foreach (new TableRows(new RecursiveArrayIterator($stmt->fetchAll(PDO::FETCH_ASSOC))) as $k => $v) {
            echo $v;
        }
        echo "</table>";

        // Get enrollment information for the user
        $stmt1 = $conn->prepare("SELECT e.CompletionStatus, c.Description FROM users AS u 
                                  LEFT JOIN enrollment AS e ON u.ID = e.UserID 
                                  LEFT JOIN course AS c ON e.CourseID = c.ID 
                                  WHERE e.UserID = :userid");
        $stmt1->bindParam(':userid', $userid);
        $stmt1->execute();

        if (checkResults($stmt1)) {
            echo "<table>";
            echo "<tr><th>Completion Status</th><th>Description</th></tr>";
            foreach (new TableRows(new RecursiveArrayIterator($stmt1->fetchAll(PDO::FETCH_ASSOC))) as $k => $v) {
                echo $v;
            }
            echo "</table>";
        }
    }

    // Function to get course information
    function getCourseInformation($conn, $courseid) {
        $stmt = $conn->prepare("SELECT c.ID as 'Course ID', c.Description FROM course AS c WHERE c.ID = :courseid");
        $stmt->bindParam(':courseid', $courseid);
        $stmt->execute();

        if (!checkResults($stmt)) {
            return;
        }

        echo "<table>";
        echo "<tr><th>Course ID</th><th>Description</th></tr>";
        foreach (new TableRows(new RecursiveArrayIterator($stmt->fetchAll(PDO::FETCH_ASSOC))) as $k => $v) {
            echo $v;
        }
        echo "</table>";

        // Get enrollment information for the course
        $stmt1 = $conn->prepare("SELECT e.CompletionStatus, u.ID as 'User ID', u.firstname, u.surname 
                                 FROM enrollment AS e 
                                 LEFT JOIN users AS u ON e.UserID = u.ID 
                                 WHERE e.CourseID = :courseid");
        $stmt1->bindParam(':courseid', $courseid);
        $stmt1->execute();

        if (checkResults($stmt1)) {
            echo "<table>";
            echo "<tr><th>Completion Status</th><th>User ID</th><th>First Name</th><th>Last Name</th></tr>";
            foreach (new TableRows(new RecursiveArrayIterator($stmt1->fetchAll(PDO::FETCH_ASSOC))) as $k => $v) {
                echo $v;
            }
            echo "</table>";
        }
    }
    ?>
</body>
</html>
