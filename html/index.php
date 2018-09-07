<!DOCTYPE html>
<html lang="en-GB">
<head>
    <?php require("common.php"); ?>
    <title>The D1 algorithm site</title>
    <link rel="stylesheet" href="/css/stylesheet.css">
    <style>
        .linkbutton {
            display: block;
            padding: 30px 5px;
            margin: 10px auto;
            font-size: 2em;
            height: auto;
            width: 90%;
        }
        .centered {
            text-align: center;
        }
    </style>
</head>

<body>

    <?php require("banner.php"); ?>

    <h1 class="centered">Welcome to the D1 algorithm site!</h1>
    <p class="centered">This site demonstrates the key D1 algorithms and includes some quizzes to test your knowledge.</p>
    
    <!--[if lt IE 11]>
    <p style="text-align: center;">Please upgrade to a modern browser. Old versions of Internet Explorer are not supported, and may expose you to security risks.</p>
    <![endif]-->

    <noscript>
        <h1 class="centered">This site requires JavaScript to function correctly. Please enable JavaScript in your browser.</h1>
    </noscript>

    <button class="linkbutton" onclick="window.location.href='choice.php'">Select an algorithm or a quiz to get started.</button>

</body>

</html>