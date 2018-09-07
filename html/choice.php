<!DOCTYPE html>
<html lang="en-GB">
<head>
    <?php require("common.php"); ?>
    <title>The D1 algorithm site</title>
    <link rel="stylesheet" href="/css/stylesheet.css">
    <link rel="stylesheet" href="/css/algochoice.css">
    <?php require("jquery.php"); ?>
    <script src="/scripts/dropdown.js"></script>
</head>
<body>

    <?php require("banner.php"); ?>

    <br />

    <div class="buttonwrap">
        <button class="linkbutton drop">Number sorting algorithms</button>
        <div class="subbox">
            <br />
            <button class="linkbutton" onclick="window.location.href='/sorting/bubblesort.php'">Bubblesort</button>
            <br />
            <button class="linkbutton" onclick="window.location.href='/sorting/shuttlesort.php'">Shuttle/cocktail-shaker sort</button>
        </div>
    </div>

    <br />

    <div class="buttonwrap">
        <button class="linkbutton drop">Graph algorithms</button>
        <div class="subbox">
            <br />
            <button class="linkbutton" onclick="window.location.href='/graph'">Proof of concept</button>
        </div>
    </div>

    <br />

    <div class="buttonwrap">
        <button class="linkbutton drop">Revison quizzes</button>
        <div class="subbox">
            <br />
            <button class="linkbutton" onclick="window.location.href='/quiz.php'">Go to the quizzes</button>
            <br />
            <button class="linkbutton" onclick="window.location.href='/signup.php'">Signup</button>
            <br />
            <button class="linkbutton" onclick="window.location.href='/teacher.php'">Teacher login</button>
            <br />
            <button class="linkbutton" onclick="window.location.href='/addteacher.php'">Add a teacher connection</button>
        </div>
    </div>
    
</body>

</html>