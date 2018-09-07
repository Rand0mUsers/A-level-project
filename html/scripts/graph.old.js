"use strict"

/**
 * A function that prompts the user for a message
 * @param {string} [promptMessage] The message to use in the dialog box
 * @param {string} [defaultMessage] The default response
 * @returns {string} The user's response
 */
function getUserInput(promptMessage, defaultMessage) {
    var response = "";
    while (response === "" || response === null) {
        response = prompt(promptMessage, defaultMessage);
        if (response === "" || response === null) {
            alert("Please enter a value.");
        }
    }
    return response;
}
/**
 * Gets a positive integer from the user.
 * @param {string} [promptMessage] The message to use in the prompt
 * @param {string} [defaultNumber] The default value
 * @returns {number} A user-entered integer
 */
function getUserInteger(promptMessage, defaultNumber) {
    var response = null;
    while (response === null || !Number.isInteger(response) || response < 0) {
        response = parseInt(prompt(promptMessage, defaultNumber));
        if (!Number.isInteger(response) || response < 0) {
            alert("Please enter a positive integer.");
        }
    }
    return response;
}

/**
 * Function that creates a random ID for an edge
 * @returns {string} A 10 character random string
 */
function getRandomID() {
    var randomID = "";
    var alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    // Generate a random 10 character string - sufficient to minimise probability of a non-unique
    // edge ID
    for (var i = 0; i < 10; i++) {
        randomID += alphabet.charAt(
            Math.floor(
                Math.random() * alphabet.length
            )
        );
    }
    return randomID;
}

/**
 * Turns an integer into a letter string e.g.
 * 1 -> A
 * 2 -> B
 * 26 -> Z
 * 27 -> AA
 * 28 -> AB
 * @param {number} number The number to convert
 * @returns {string} The letter representation
 */
function intToLetters(number) {
    if (number === 1) {
        return "A";
    }
    const alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var numberOfLetters = Math.ceil(Math.log(number) / Math.log(26));
    // console.log(numberOfLetters);
    var output = "";

    for (var i = numberOfLetters - 1; i >= 0; i--) {
        // console.log("digit", i);
        var index = (Math.floor(number / 26 ** i) - 1) % 26;
        // console.log("index", index);
        output += alphabet[index];
    }

    return output;
}

/**
 * Tests if the graph is cyclic
 */
function isCyclic(graph) {
    // credit: https://www.geeksforgeeks.org/detect-cycle-undirected-graph/
    // use graph.neighborhood(nodeid) to get edges connected to nodes and neighbouring edges
    function isCyclicUtil(v, visited, parent) {
        visited[v] = true;
        var neighborNodes = graph.neighborhood(v).nodes;
        for (var i = 0; i < neighborNodes.length; i++) {
            if (!visited[i]) {
                if (isCyclicUtil(nodes[i], visited, nodes[v])) {
                    return true;
                }
            } else if (parent != i) {
                return true;
            }
        }
        return false;
    }
    return;
}

// Create a new sigma instance
var s = new sigma({
    renderer: {
        container: document.getElementById('graphcontainer'),
        type: 'canvas'
    },
    settings: {
        edgeLabelSize: 'proportional',
        defaultLabelColor: '#fff', // white nodes
        defaultNodeColor: '#fff', // white labels
        defaultEdgeColor: '#fff', // white edges
        defaultEdgeLabelColor: '#fff', // white edge labels
        labelThreshold: 0, // make labels always visible
        edgeLabelThreshold: 0,
        defaultEdgeLabelSize: 12,
        minEdgeSize: 0,
        maxEdgeSize: 5
    }
});

/**
 * Function that creates several random nodes
 */
function initialNodes() {
    for (var i = 1; i <= 10; i++) {
        s.graph.addNode({
            id: "n" + intToLetters(i),
            label: intToLetters(i),
            size: Math.max(Math.log10(i), 1),
            x: Math.random(),
            y: Math.random()
        });
    }

    for (var i = 1; i <= 10; i++) {
        s.graph.addEdge({
            id: "e" + i,
            source: "n" + intToLetters(1 + Math.round(Math.random() * 9)),
            target: "n" + intToLetters(1 + Math.round(Math.random() * 9)),
            label: i.toString(),
            weight: i,
            type: 'line',
            size: (1 / Math.sqrt(i))
        });
    }
    s.refresh();
}

initialNodes();

// Initialises node dragging
var dragListener = sigma.plugins.dragNodes(s, s.renderers[0]);

/**
 * Sensible default ForceAtlas2 settings:
 * linLogMode - leads to a better layout,
 * barnesHutOptimize - layouts tend to form faster with negligable impact on quality,
 * slowdown - increases layout speed
 */
const FORCE_ATLAS_CONFIG = {
    linLogMode: true,
    barnesHutOptimize: true,
    slowdown: 0.1
}

s.startForceAtlas2(FORCE_ATLAS_CONFIG);

// kill ForceAtlas2 after 1s so nodes can be interacted with safely
setTimeout(function() {
    s.killForceAtlas2();
}, 1000)

// refresh graph to ensure all out changes are pushed through
s.refresh();

// Handles adding a single node
$('#newnode').on("click", function() {
    try {
        // Prompt for node name to add
        var nodeName = getUserInput("Node name");
        // Add node
        s.graph.addNode({
            id: 'n' + nodeName,
            label: nodeName,
            size: 1,
            x: Math.random(),
            y: Math.random()
        });
        // Update graph
        s.refresh();
    } catch (err) {
        // sigma throws an exception if the node ID already exists
        alert("That node already exists.");
    }

});

// Handles adding multiple nodes in one go
$('#manynodes').on("click", function() {
    var nodeType = prompt("How should the nodes be named? Choose from number or letter.\n(You can type n for number or l for letter)");

    // Stops if invalid input given
    if (!["letter", "number", "l", "n"].includes(nodeType)) {
        return;
    }

    // Gets number of new nodes to create
    var numberOfNodes = getUserInteger("How many nodes to create?", "0");

    // Break if 0 given
    if (numberOfNodes === 0) {
        return;
    }

    // Clear the graph
    s.graph.clear();

    if (nodeType.toLowerCase() === "number" || nodeType.toLowerCase() === "n") {
        // Numerical node naming - add nodes with numerical names
        for (var i = 1; i <= numberOfNodes; i++) {
            s.graph.addNode({
                id: 'n' + i,
                label: i.toString(),
                size: 1,
                x: Math.random(),
                y: Math.random()
            });
        }
    } else {
        // Letter-based naming - use intToLetters to convert number -> letters, then use that for name and ID
        for (var i = 1; i <= numberOfNodes; i++) {
            s.graph.addNode({
                id: 'n' + intToLetters(i),
                label: intToLetters(i),
                size: 1,
                x: Math.random(),
                y: Math.random()
            });
        }
    }

    // Update graph
    s.refresh();
});

// Handles adding edges
$('#newedge').on("click", function() {
    var edges = prompt("Enter space-seperated pairs of nodes to connect e.g. AB BC");

    // Break if blank response given or cancel clicked
    if (edges === "" || edges === null) {
        return;
    }

    // Process input...
    edges = edges.replace(/[^0-9A-Za-z .]/g, " ") // replace anything that isn't a letter, number or space with a space
        .replace(/ +/g, " ") // reduce any consecutive sets of spaces to a single space
        .replace(/^ /g, "") // remove any spaces on the start of the string
        .replace(/ $/, "") // remove any spaces on the end of the string
        .split(" "); // split by spaces, leaving a list of consecutive letters only

    /** Variable holding assembled error message */
    var errMessage = "";

    // For each potential edge to add (edge given as string)
    edges.forEach(function(edge) {
        // See if string is two characters long
        if (edge.length === 2) {
            // Try to add the edge, otherwise append to errMessage
            try {
                s.graph.addEdge({
                    id: 'e' + getRandomID(),
                    source: 'n' + edge[0],
                    target: 'n' + edge[1]
                });
            } catch (err) {
                if (errMessage.length === 0) {
                    errMessage += "Invalid node(s):";
                }
                errMessage += " " + edge;
            }
        } else {
            // Invalid due to incorrect number of nodes given - add to errMessage
            if (errMessage.length === 0) {
                errMessage += "Invalid node(s):";
            }
            errMessage += " " + edge;
        }
    });
    // If errMessage isn't blank
    if (errMessage !== "") {
        alert(errMessage);
    }

    // Update graph
    s.refresh();
});

// Handles clearing the graph
$('#clear').on("click", function() {
    // Clear the graph
    s.graph.clear();
    // Update the graph
    s.refresh();
});

// Handles automatic layout on request
$('#autolayout').on("click", function() {
    s.startForceAtlas2(FORCE_ATLAS_CONFIG);

    // Run the ForceAtlas2 algorithm for 1 second
    setTimeout(function() {
        s.killForceAtlas2();
    }, 1000);
});