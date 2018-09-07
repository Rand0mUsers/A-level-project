<!DOCTYPE html>
<html lang="en-GB">
<head>
    <?php require("common.php"); ?>
    <title>D1 Quizzes</title>
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,500" rel="stylesheet">
    <link rel="stylesheet" href="/css/quiz.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        function onSubmit(token) {
            document.getElementById('quizform').submit();
        }

        function validate(event) {
            event.preventDefault();
            if (document.getElementById('quizform').checkValidity()) {
                grecaptcha.execute();
            } else {
                alert("Please complete all questions and enter your login details.");
            }
        }

        function onload() {
            try {
                document.getElementById('submitbutton').onclick = validate;
            } catch (err) {
                ; // Submit button not created - do nothing as this is normal behaviour
            }
            
        }
    </script>
    
</head>
<body>
<?php
// Includes config data and common functions
require('/var/www/config.php');

// Database connection object
$pdo = get_PDO();

// Function used to restore selected option from dropdown box
// val - the field value to restore
// Usage: when building an <option> tag, use "<option {other options}" . restore(name, value) . ">..."
function restore($questionid, $val) {
    if (isset($_POST[$questionid]) and $_POST[$questionid] === $val) {
        return " selected";
    }
}

// Takes quiz data from the database and builds an HTML form from it
function buildquiz() {
    global $pdo;

    // Prepare and execute query to retrieve question data for given test
    $quizstmt = $pdo->prepare("SELECT QuestionID, Prompt, Solution, Options FROM Question WHERE TestID = :quizid");
    $quizstmt->execute([
        'quizid' => $_GET['quiz']
    ]);

    // Checks if at least 1 question is available in the quiz
    if ($quizstmt->rowcount() !== 0) {

        // Sets beginning of form
        echo "<form id=\"quizform\" method=\"post\">\n";

        // Iterate through the questions, creating the dropdown box for each
        $question_number = 1;
        foreach ($quizstmt as $question) {

            // Output question number and prompt - an underscore in the prompt will be replaced by the answer
            $paragraph = "<p>" . htmlentities($question_number) . " - " . htmlentities($question['Prompt']) . "</p>\n";
            $question_number++;

            // The correct answer is given in the Solution field - at least one incorrect response
            //   will be given in the Options field as a comma-seperated list.
            // The Options and Solution are combined and their order randomised so they can't be distinguised
            $options = explode(",", $question['Options']);
            $options[] = $question['Solution'];
            shuffle($options);

            // Starts to build the dropdown box options, including a default "Choose..." prompt that can't be selected
            $selection_box = "<select name=\"" . htmlspecialchars($question['QuestionID']) . "\" required>\n
                <option disabled selected value style=\"display: none;\">Choose...</option>\n";

            // Each possible response is turned into an option value
            foreach ($options as $opt) {
                $selection_box .= "<option" . restore($question['QuestionID'], $opt) . ">". htmlentities($opt) . "</option>\n";
            }

            // Select tag closed
            $selection_box .= "</select>\n";

            // Combine the selection box HTML with the question prompt and output
            echo str_replace("_", $selection_box, $paragraph);
        }

        // Output remaining form bits - to allow validation before submitting, a programmatic
        //   CAPTCHA challenge is used.
        ?> 
            <input type="text" placeholder="Username" name="username" maxlength="20" required>
            <input type="password" placeholder="Password" name="password" required>
            <br />
            <div class="g-recaptcha" data-sitekey="6LfQ10wUAAAAAEGvk5PTNhT9FN74RUBriK1Uehtd" data-callback="onSubmit" data-size="invisible"></div>
            <button id="submitbutton">Submit</button>
        </form>
        <?php

    } else {
        // Quiz doesn't exist, or doesn't have any questions
        // Send error message and quit
        http_response_code(404);
        echo "<h1>That quiz doesn't exist.</h1>";
        die();
    }
}


if (!isset($_GET['quiz'])) {
    // Quiz number is not set in URL - fetch and display quizzes from database to allow user to select
    $quizstmt = $pdo->query("SELECT * FROM Test");
    echo "<h1>List of quizzes:</h1>\n";
    
    // For each quiz available, create a link to it
    foreach ($quizstmt as $row) {
        echo "<p class=\"quizname\"><a href=\"/quiz.php?quiz=" . htmlentities($row['TestID']) . "\">" . htmlentities($row['Name']) . "</a></p>\n";
    }

    // Add a back button to return to the choice page
    echo "<p><a href=\"/choice.php\">Back</a></p>";


} elseif (!isset($_POST['username'], $_POST['password'])) {
    // Quiz number is set but the quiz hasn't been filled out - display quiz and login info
    buildquiz();

} elseif (isset($_GET['quiz'], $_POST['username'], $_POST['password'])) {
    // A quiz number has been given, and the login fields are filled out, so
    //   the student's login should be verified and their answers graded

    // Verify CAPTCHA solution and exit if invalid
    if (!validate_g_captcha($config['invisible_captcha_secret'], $_POST['g-recaptcha-response'])) {
        echo "<p class=\"error\">CAPTCHA validation failed.</p>";
        die();
    }

    // Check user login details
    if (validate_login($_POST['username'], $_POST['password'], ACCOUNT_STUDENT)) {

        // Prepare database queries
        $updatestmt = $pdo->prepare("INSERT INTO TestResult (StudentID, TestID, Score) VALUES (:studentID, :testID, :score)");
        $get_questions = $pdo->prepare("SELECT QuestionID, Prompt, Solution, Options FROM Question WHERE TestID = :quizid");

        // Get student data
        $userstmt = $pdo->prepare("SELECT * FROM Student WHERE Username = :username");
        $userstmt->execute([
            'username' => $_POST['username']
        ]);
        $userdata = $userstmt->fetch();

        // Get the questions associated with this quiz
        $get_questions->execute([
            'quizid' => $_GET['quiz']
        ]);
        $question_data = $get_questions->fetchAll();

        echo "<h1>Hi " . htmlentities($userdata['Name']) . ". Here are your results.</h1><ol>";


        // Compare the list of test questions with the user's responses to determine their score
        $score = 0;
        // For each question associated with the test...
        foreach ($question_data as $question) {

            // Check a response has been given for this question
            if (!isset($_POST[$question['QuestionID']])) {
                http_response_code(400);
                echo "</ol>\n<p class=\"error\">Bad request.</p>\n";
                die();
            } 

            // See if the response matches the answer and provide appropriate output
            // The original question prompt is used to give the answer in context e.g.
            // Correct response - D1 is about *discrete* maths
            // Incorrect response - Incorrect - you said "D1 is about *pure* maths"
            //   when the answer was "D1 is about *discrete* maths"
            if ($_POST[$question['QuestionID']] === $question['Solution']) {
                echo "<li class=\"answer correct\">Correct! " . 
                    str_replace("_", 
                        "<strong>" . htmlentities($question['Solution']) . "</strong>", 
                        htmlentities($question['Prompt'])
                    ) . 
                "</li>\n";
                $score++;
            } else {
                echo "<li class=\"answer incorrect\">Incorrect - you said \"" . 
                    str_replace("_", 
                        "<strong>" . htmlentities($_POST[$question['QuestionID']]) . "</strong>", 
                        htmlentities($question['Prompt'])
                    ) . 
                    "\" when the answer was \"" . 
                    str_replace("_", 
                        "<strong>" . htmlentities($question['Solution']) . "</strong>",
                        htmlentities($question['Prompt'])
                    ) . 
                "\"</li>";
            }
        }
        echo "</ol>";

        // Display total score as a fraction
        echo "<h2>Your total score was <sup>" . htmlentities($score) . "</sup>/<sub>" . htmlentities($get_questions->rowCount()) . "</sub>.</h2>";
        echo "<p><a href=\"quiz.php\">Back</a></p>";
        
        // Commit score to DB
        $updatestmt->execute([
            'studentID' => $userdata['Username'],
            'testID' => $_GET['quiz'],
            'score' => $score
        ]);
    } else {
        // Login incorrect
        echo "<p class=\"error\">Incorrect username or password.</p>";
    }

} else {
    // Some other combination that makes no sense, e.g. username and password filled
    //   but quiz number not set
    http_response_code(400);
    echo "<h1>Invalid request.</h1>";
}
?>
    <script>
        // Set up the CAPTCHA
        onload()
    </script>
</body>

</html>