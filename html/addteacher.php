<!DOCTYPE html>
<html lang="en-GB">
<head>
    <?php require("common.php"); ?>
    <title>Choose your teacher</title>
    <link href="/css/signup.css" rel="stylesheet">
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
    <h1>Enter details below to add a teacher connection:</h1>
    <form method="post" autocomplete="off">
        <input type="text" name="username" placeholder="Username" maxlength="20" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="text" name="teacherid" placeholder="Teacher's username" maxlength="20" />
        <div class="g-recaptcha" data-sitekey="6Le4dEwUAAAAALzICK3OC6XvlD8emmpi0vTvp2hn" data-theme="dark"></div>
        <input type="submit" value="Use this teacher" />
    </form>
    <p>Setting a teacher connection allows that teacher to see all your past and future results.</p>
    <p>You can only have one teacher connection; changing it allows that teacher to see all your scores and withdraws access for your current teacher.</p>
    <p>Leave the teacher name blank to remove your teacher connection.</p>
    <?php 
    // Include configuration data
    require('/var/www/config.php');

    // Check both username and passwords set
    if (isset($_POST['username'], $_POST['password'])) {

        // Check CAPTCHA solution valid
        if (validate_g_captcha($config['normal_captcha_secret'], $_POST['g-recaptcha-response'])) {

            // Validate login
            if (validate_login($_POST['username'], $_POST['password'], ACCOUNT_STUDENT)) {
                // Login successful
                echo "<p>Login successful.</p>";
                
                // DB connection object
                $pdo = get_PDO();

                try {

                    // Check if teacher username left blank - if so, remove teacher link from DB
                    if (!isset($_POST['teacherid']) or $_POST['teacherid'] === "") {
                        // Remove teacher
                        $stmt = $pdo->prepare("UPDATE Student SET TeacherID=NULL WHERE Username=:username");
                        $stmt->execute([
                            'username' => $_POST['username']
                        ]);
                        echo "<p>Done - removed teacher access.</p>";
                    } else {
                        
                        // Attempt to set new teacher username
                        $stmt = $pdo->prepare("UPDATE Student SET TeacherID=:teacherid WHERE Username=:username");
                        $stmt->execute([
                            'username' => $_POST['username'],
                            'teacherid' => $_POST['teacherid']
                        ]);
                        echo "<p>Done - your results are now accesible by " . htmlentities($_POST['teacherid']) . ".</p>";
                    }
                } catch (PDOException $e) {
                    // Check if exception thrown due to invalid teacher username
                    if ($stmt->errorCode() === "23000") {
                        // Constraint violation due to invalid teacher username
                        echo "<p class=\"error\">That teacher doesn't exist.</p>";
                    }
                }

            } else {
                echo "<p class=\"error\">Login failed - incorrect username or password.</p>";
            }

        } else {
            echo "<p class=\"error\">CAPTCHA validation failed.</p>";
        }
    }
    ?>
    <p><a href="/choice.php">Back</a></p>
</body>
</html>