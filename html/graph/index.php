<!DOCTYPE html>
<html lang="en-GB">
<head>
    <?php require("../common.php"); ?>
    <title>Graphs</title>
    <link rel="stylesheet" href="/css/stylesheet.css" />
    <link rel="stylesheet" href="/css/graph.css" />
    <?php require("../jquery.php"); ?>
    <script src="/sigma/sigma.min.js"></script>
    <script src="/sigma/plugins/sigma.plugins.animate.min.js"></script>
    <script src="/sigma/plugins/sigma.layout.forceAtlas2.min.js"></script>
    <script src="/sigma/plugins/sigma.plugins.dragNodes.min.js"></script>
    <script src="/sigma/plugins/sigma.plugins.neighborhoods.min.js"></script>
    <script src="/sigma/plugins/sigma.renderers.edgeLabels.min.js"></script>
    <script src="/scripts/graph.js" defer></script>
</head>
<body>

    <?php require("../banner.php"); ?>

    <div id="container">
        <div id="graphcontainer"></div>

        <div id="controls">
            <button id="newnode">Add one node</button>
            <button id="manynodes">Clear and add nodes</button>
            <button id="newedge">Add edges</button>
            <button id="clear">Clear all</button>
            <button id="autolayout">Autolayout</button>
        </div>
    </div>

</body>
</html>