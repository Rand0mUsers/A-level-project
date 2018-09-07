<!DOCTYPE html>
<html lang="en-GB">
<head>
    <?php require("common.php"); ?>
    <title>Teacher login</title>
    <link rel="stylesheet" href="/css/signup.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,500" rel="stylesheet"> 
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <h1>Enter password to view...</h1>
    <form method="post">
        <input type="text" name="username" maxlength="20" placeholder="Username">
        <input type="password" name="password" placeholder="Password">
        <div class="g-recaptcha" data-sitekey="6Le4dEwUAAAAALzICK3OC6XvlD8emmpi0vTvp2hn" data-theme="dark"></div>
        
        <input type="submit" name="submitbutton" value="Get results">
    </form>
    <p><a href="/choice.php">Back</a></p>
    <?php
    // Include common configuration data
    require('/var/www/config.php');

    if (isset($_POST['submitbutton'])) {
        // Form submitted - check CAPTCHA
        if (validate_g_captcha($config['normal_captcha_secret'], $_POST['g-recaptcha-response'])) {
            // CAPTCHA validated, check login details

            if (validate_login($_POST['username'], $_POST['password'], ACCOUNT_TEACHER)) {
                // Teacher login successful

                // Create DB connection object
                $pdo = get_PDO();

                // Prepare and execute a query of student results for that teacher
                $studentstmt = $pdo->prepare("SELECT Name, Username FROM Student WHERE TeacherID=:teacherid ORDER BY Name");
                $studentstmt->execute([
                    'teacherid' => $_POST['username']
                ]);
                $students = $studentstmt->fetchAll();

                // Set up a query to find test scores by username
                $getscores = $pdo->prepare("SELECT Test.Name, TestResult.Score, TestResult.ResultTime 
                    FROM TestResult JOIN Test ON (TestResult.TestID = Test.TestID) 
                    WHERE StudentID=:username 
                    ORDER BY TestResult.ResultTime DESC
                ");

                // Check to see how many students the teacher has been linked to
                if (count($students) != 0) {
                    // The teacher has at least 1 linked student

                    // Display results for each student in turn
                    foreach ($students as $sdnt) {
                        // Show the student name and username
                        echo "<h2>\"" . htmlentities($sdnt['Name']) . "\"</h2>\n";
                        echo "<p>" . htmlentities($sdnt['Username']) . "</p>\n";

                        // Fetch the student's scores
                        $getscores->execute([
                            'username' => $sdnt['Username']
                        ]);
                        $data = $getscores->fetchAll();
                        
                        // Check if the student has some scores to display
                        if (count($data) !== 0) {
                            // Set up table headings
                            echo "<table>
                            <tr>
                                <th>Test name</th>
                                <th>Score</th>
                                <th>Time</th>
                            </tr>";

                            // Add a table row for each result
                            foreach ($data as $result) {
                                echo "<tr>
                                <td>" . htmlentities($result['Name']) . "</td>
                                <td>" . htmlentities($result['Score']) . "</td>
                                <td>" . htmlentities($result['ResultTime']) . "</td>
                                </tr>";
                            }
                            // End table
                            echo "</table>";
                        } else {
                            // No student scores recorded
                            echo "<p>No scores for this student.</p>";
                        }
                    }
                } else {
                    // The teacher has no students assigned - display a message to that effect
                    echo "<p>You have logged in successfully, but you don't have any students linked to your account.</p>";
                }

            } else {
                // CAPTCHA validation fine but login details wrong (username or password don't match)
                echo "<p class=\"error\">Login failed - please check your username and password.</p>";
                die();
            }
        } else {
            // CAPTCHA validation failed - e.g. invalid solution
            echo "<p class=\"error\">CAPTCHA validation failed.</p>";
            die();
        }
    }
    ?>
</body>
</html>