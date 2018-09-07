<!DOCTYPE html>
<html lang="en-GB">
<head>
    <?php require("common.php"); ?>
    <title>D1 signup</title>
    <script src="/scripts/signup_reqs.js"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <link href="https://fonts.googleapis.com/css?family=Raleway:300" rel="stylesheet"> 
    <link href="/css/signup.css" rel="stylesheet">
</head>
<body>
    <h1>Enter your details below to sign up...</h1>
    <form method="post" onsubmit="return check_cap()" autocomplete="off">
        <label>Select your account type. Students can complete quizzes and register to a teacher; teachers can view their student's results.</label>
        <br />
        <input type="radio" id="teacher" name="acctype" value="teacher" required>
        <label for="teacher">I am a teacher</label>
        <br />
        <input type="radio" id="student" name="acctype" value="student" required>
        <label for="student">I am a student</label>

        <br />

        <input type="text" name="name" placeholder="Enter your name" maxlength="100" id="name" <?php r('name') ?> required>
        
        <input type="text" name="username" placeholder="Choose a username" maxlength="20" id="username" onkeyup="checkUsername()" <?php r('username') ?> required>
        <p id="namealert" style="display: none">The username must consist of at least 4 characters. Allowed characters are letters, numbers and underscores.</p>

        <input type="password" name="password" placeholder="Password" id="password" onkeyup="passwordRequirementCheck()" <?php r('password') ?> required>
        <div id="passreqs"></div>

        <input type="password" name="verifypassword" placeholder="Verify password" id="verifypassword" onkeyup="comparePasswords()" <?php r('verifypassword') ?> required>
        <p id="matchalert" style="display: none">Passwords do not match!</p>

        <div class="g-recaptcha" data-sitekey="6Le4dEwUAAAAALzICK3OC6XvlD8emmpi0vTvp2hn" data-theme="dark" data-callback="cap_complete" data-expired-callback="cap_expired"></div>

        <button type="submit" name="submitbutton">Register</button>
        
    </form>

    <p><a href="/choice.php">Back</a></p>
    
    <?php
    // Includes common functions
    require('/var/www/config.php');

    // Function used to restore the content of field inputs on error
    // If the form data id given by $name is set, r($name) will be replaced with 'value="..."'
    // This sets the initial data value of the form field
    function r($name) {
        if (isset($_POST[$name])) {
            echo "value=\"" . htmlentities($_POST[$name]) . "\"";
        }
    }

    if (isset($_POST['submitbutton'])) {
        // The following code runs when the form has been submitted
        // The form data must be validated first, otherwise the script dies with an error message
        // This is designed for non-JS users; everyone else has error messages generated clientside
        //   before the form is submitted

        // Conveniance variables
        $name = $_POST['name'];
        $username = strtolower($_POST['username']);
        $password = $_POST['password'];
        $verifypassword = $_POST['verifypassword'];
        
        if ($password !== $verifypassword) {
            echo "<p>Passwords must match.</p>";
            die();
        }

        if (strlen($name) > 100) {
            echo "<p>Name cannot be longer than 100 characters.</p>";
            die();
        }

        if (strlen($username) > 20) {
            echo "<p>Username cannot be longer than 20 characters.</p>";
            die();
        }

        // Tests password complexity against requirements
        if (
            strlen($password) < 8                   // minimum password length
            or !preg_match("/[A-Z]/", $password)    // must contain uppercase...
            or !preg_match("/[a-z]/", $password)    // ...lowercase...
            or !preg_match("/[0-9]/", $password)    // ...and a number
        ) {
            echo "<p>Password must contain at least one uppercase letter, lowercase letter, and a number, and it must be at least 8 characters long.</p>";
            die();
        }

        // On reaching this point, all basic complexity and length conditions have been satisfied.
        // The password will then be tested against the most popular 10,000 passwords (as per https://github.com/danielmiessler/SecLists)
        //   and the account setup rejected if the password is too common

        if (strpos(
            file_get_contents("common_passwords.txt"), strtolower($password) /* see if the password submitted is found in the password list */
        ) !== false) {
            echo "<h2>Your password is extremely common!.</h2>\n
                <p>Your password features in a list of the 10,000 most common passwords.</p>\n
                <p>You can't sign up until you use a stronger password.</p>";
            die();
        }

        // All basic requirements satisfied.

        // CAPTCHA validation
        if (validate_g_captcha($config['normal_captcha_secret'], $_POST['g-recaptcha-response'])) {

            // CAPTCHA successful - attempt to add the user to the database
            try {
                // Create a database connection
                $signuppdo = get_PDO();

                // Set the statement according to the account type, add data and execute
                if ($_POST['acctype'] === "teacher") {
                    // Teacher account
                    $stmt = $signuppdo->prepare("INSERT INTO Teacher (Username, Name, PasswordHash) VALUES (:username, :name, :passwordhash)");
                    $passwordhash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt->execute([
                        'username' => $username,
                        'name' => $name,
                        'passwordhash' => $passwordhash
                    ]);
                    echo "<p>Teacher signup complete. Welcome, " . htmlentities($name) . ".</p>\n
                    <a href=\"/choice.php\">Back</a>\n";


                } elseif ($_POST['acctype'] === "student") {
                    // Student account
                    $stmt = $signuppdo->prepare("INSERT INTO Student (Username, Name, PasswordHash) VALUES (:username, :name, :passwordhash)");
                    $passwordhash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt->execute([
                        'username' => $username,
                        'name' => $name,
                        'passwordhash' => $passwordhash
                    ]);
                    echo "<p>Student signup complete. Welcome, " . htmlentities($name) . ".</p>\n
                    <a href=\"/choice.php\">Back</a>\n";

                } else {
                    // Account type in POST request not set or invalid
                    echo "<h2>Please specify an account type - teacher or student.</h2>";
                    die();
                }
                
            } catch (PDOException $exc) {
                // An exception can be generated if the username already exists
                $errcode = $exc->getCode();
                if ($errcode == 23000) {
                    // This indicates a uniqueness constraint violation -
                    //   in other words, the username is already in use
                    echo "<p>Username already in use; please try a different username.</p>";
                    die();
                }
            }
        } else {
            // CAPTCHA challenge failed
            echo "<h1>reCAPTCHA challenge failed - please try again.</h1>";
            die();
        }
    }
    ?>

</body>
</html>