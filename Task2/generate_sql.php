<?php

require_once __DIR__ . '/vendor/autoload.php';

$faker = Faker\Factory::create();

// Open a file to write the SQL statements
$file = fopen('sample_data.sql', 'w');

// To ensure uniqueness, we will store generated values in arrays
$usernames = [];
$coursenames = [];
$enrollments = [];

// Predefined list of English course names
$courseList = [
    'Introduction to Psychology',
    'Principles of Marketing',
    'Data Structures and Algorithms',
    'Financial Accounting',
    'Organic Chemistry',
    'World History',
    'Calculus I',
    'Business Ethics',
    'Introduction to Sociology',
    'Microeconomics',
    'Human Anatomy',
    'Artificial Intelligence',
    'Graphic Design Basics',
    'Environmental Science',
    'Physics I',
    'Creative Writing',
    'Project Management',
    'Political Science',
    'Philosophy of Mind',
    'Software Engineering',
    'Biostatistics',
    'Molecular Biology',
    'Digital Marketing',
    'Entrepreneurship',
    'Operations Management',
    'Civil Engineering',
    'Introduction to Programming',
    'Econometrics',
    'Database Systems',
    'Strategic Management',
];

// Generate SQL for users (100 unique users)
fwrite($file, "INSERT INTO users (firstname, surname) VALUES\n");
for ($i = 0; $i < 100; $i++) {
    do {
        $firstname = $faker->firstName;
        $surname = $faker->lastName;
        $uniqueName = $firstname . ' ' . $surname;
    } while (in_array($uniqueName, $usernames));
    
    $usernames[] = $uniqueName;
    
    $line = sprintf("('%s', '%s')%s\n",
        $firstname,
        $surname,
        $i == 99 ? ';' : ','
    );
    fwrite($file, $line);
}

// Generate SQL for courses (30 unique courses using predefined list)
fwrite($file, "\nINSERT INTO courses (description) VALUES\n");
for ($i = 0; $i < 30; $i++) {
    $description = $courseList[$i];
    
    $line = sprintf("('%s')%s\n",
        $description,
        $i == 29 ? ';' : ','
    );
    fwrite($file, $line);
}

// Generate SQL for enrollments (100 unique enrollments)
fwrite($file, "\nINSERT INTO enrollments (UserID, CourseID, CompletionStatus) VALUES\n");
for ($i = 0; $i < 100; $i++) {
    do {
        $userID = $faker->numberBetween(1, 100);  // Random UserID from 100 users
        $courseID = $faker->numberBetween(1, 30); // Random CourseID from 30 courses
        $completionStatus = $faker->randomElement(['completed', 'in progress', 'not started']);
        $enrollmentKey = "$userID-$courseID-$completionStatus";
    } while (in_array($enrollmentKey, $enrollments));
    
    $enrollments[] = $enrollmentKey;
    
    $line = sprintf("(%d, %d, '%s')%s\n",
        $userID,
        $courseID,
        $completionStatus,
        $i == 99 ? ';' : ','
    );
    fwrite($file, $line);
}

// Close the file
fclose($file);

echo "Sample SQL file with 100 unique enrollments generated successfully!\n";

// Generate another SQL file with 100,000 unique enrollments
$file = fopen('sample_data_100000.sql', 'w');

// Reset arrays for uniqueness checks
$usernames = [];
$enrollments = [];

// Generate SQL for users (100 unique users)
fwrite($file, "INSERT INTO users (firstname, surname) VALUES\n");
for ($i = 0; $i < 100; $i++) {
    do {
        $firstname = $faker->firstName;
        $surname = $faker->lastName;
        $uniqueName = $firstname . ' ' . $surname;
    } while (in_array($uniqueName, $usernames));
    
    $usernames[] = $uniqueName;
    
    $line = sprintf("('%s', '%s')%s\n",
        $firstname,
        $surname,
        $i == 99 ? ';' : ','
    );
    fwrite($file, $line);
}

// Generate SQL for courses (30 unique courses using predefined list)
fwrite($file, "\nINSERT INTO courses (description) VALUES\n");
for ($i = 0; $i < 30; $i++) {
    $description = $courseList[$i];
    
    $line = sprintf("('%s')%s\n",
        $description,
        $i == 29 ? ';' : ','
    );
    fwrite($file, $line);
}

// Generate SQL for 100,000 unique enrollments
fwrite($file, "\nINSERT INTO enrollments (UserID, CourseID, CompletionStatus) VALUES\n");
for ($i = 0; $i < 100000; $i++) {
    do {
        $userID = $faker->numberBetween(1, 100);  // Random UserID from 100 users
        $courseID = $faker->numberBetween(1, 30); // Random CourseID from 30 courses
        $completionStatus = $faker->randomElement(['completed', 'in progress', 'not started']);
        $enrollmentKey = "$userID-$courseID-$completionStatus";
    } while (in_array($enrollmentKey, $enrollments));
    
    $enrollments[] = $enrollmentKey;
    
    $line = sprintf("(%d, %d, '%s')%s\n",
        $userID,
        $courseID,
        $completionStatus,
        $i == 99999 ? ';' : ','
    );
    fwrite($file, $line);
}

// Close the file
fclose($file);

echo "Sample SQL file with 100,000 unique enrollments generated successfully!\n";
