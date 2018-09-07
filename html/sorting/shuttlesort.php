<!DOCTYPE html>
<html lang="en-GB">
<head>
    <?php require("../common.php"); ?>
    <title>Shuttlesort visualiser</title>
    <link rel="stylesheet" href="/css/stylesheet.css">
    <link rel="stylesheet" href="/css/sortstyle.css">
    <script src="/scripts/shuttlesort.js" defer></script>
    <?php require("../jquery.php"); ?>
</head>

<body>

    <?php require("../banner.php"); ?>

    <div class="displayarea">
    </div>

    
    <div id="controls-box">
        <div class="stats box hidden" id="stepbox">
            <p id="steps">Step: 0/0</p>
        </div>
        <div class="stats box hidden" id="compbox">
            <p id="comps">Comparisons: 0</p>
        </div>
        <div class="stats box hidden" id="swapbox">
            <p id="swaps">Swaps: 0</p>
        </div>

        <div class="padding"></div>

        <div class="stats box hidden">
            <p id="stepinfo"></p>
        </div>

        <div class="padding"></div>

        <div class="stepbox hidden">
            <button id="step-backward" class="control" type="button">Step back</button>
        </div>
        <div class="stepbox hidden">
            <button id="step-forward" class="control" type="button">Step forward</button>
        </div>

        <div class="pausebox hidden">
            <button id="pause" class="control" type="button">Pause</button>
        </div>

        <div class="box hidden" id="speedbox">
            <input type="range" min="1" max="10" value="6" id="speed" list="tickmarks">
            <datalist id="tickmarks">
              <option value="0">
              <option value="1">
              <option value="2">
              <option value="3">
              <option value="4">
              <option value="5">
              <option value="6">
              <option value="7">
              <option value="8">
              <option value="9">
              <option value="10">
            </datalist>
        </div>

        <button id="start" class="control" type="button">Set numbers</button>
    </div>


</body>

</html>
