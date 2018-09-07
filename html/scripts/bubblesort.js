"use strict";

// When the document loads, initalise handlers for user interactions
$(document).ready(function() {
    $("#start").on("click", getNumsAndRun);
    $("#pause").on("click", pause);
    $("#step-forward").on("click", stepForward);
    $("#step-backward").on("click", stepBackward);
    $("#speed").on("change", function() {
        if (!paused) {
            if (intervalID !== false) {
                clearInterval(intervalID);
            }
            calculateInterval();
            intervalID = setInterval(advance, interval);
        }
    });
});

/**
 * A function that extends array. Swaps the array elements at the given indexes in-place.
 * Call <arrayname>.swap(0, 1) to swap the first two elements, for example.
 */
Array.prototype.mySwap = function(indexA, indexB) {
    var input = this;

    var temp = input[indexA];
    input[indexA] = input[indexB];
    input[indexB] = temp;
}

/**
 * Given a list of numbers, calculates a scaling factor to use that ensures they all fit on-screen.
 * @param {number[]} numbers A list of numbers to use
 * @returns {number} The scaling factor
 * @todo Use a logarithmic scale when the difference between values is significant?
 */
function calculateScalingFactor(numbers) {
    var max = numbers.reduce(
        function(a, b) {
            return Math.max(a, b);
        }
    );
    // map max value to 100% of target height
    return ($('.displayarea').height() - 2) / max;
}

/**
 * Returns some HTML for a single element, given the current scaler value and the numerical value of the element
 * @param {number} scaler The scale factor to use
 * @param {number} value The number value to create
 * @returns {string} The HTML string representing an element
 */
function createSingleElement(scaler, value, log) {
    return $("<p class=\"item\" style=\"height: " + Math.round(scaler * value) + "px\">" + value + "</p>");
}

/**
 * Given a list of elements, clears the displayarea and adds the elements to it.
 * @param {element[]} elements A list of elements to process
 */
function createElements(elements) {
    $('.displayarea').empty();

    var scaler = calculateScalingFactor(elements.map(function(e) {
        return e.value;
    }));

    $('.displayarea').append(elements.map(function(element) {
        var e = createSingleElement(scaler, element.value);

        if (element.locked) {
            e.addClass("locked");
        }

        return e;
    }));
}

/**
 * An element represents a number used in the sort.
 * @class
 * @param {number} value The numberical value of the element
 * @param {boolean} locked Boolean representing if the element is locked or not
 */
function element(value, locked) {
    // the numerical value of the element
    this.value = value;

    // whether or not the element has been locked
    //  (elements are locked once the algorithm determines they're correctly placed)
    if (locked !== true && locked !== false) {
        this.locked = false;
    } else {
        this.locked = locked;
    }
}

// Constants used for different types of move
/** Constant representing a move with a comparison only. */
const COMPARE_ONLY = 1;
/** Constant representing a move with a comparison and swap. */
const COMPARE_AND_SWAP = 2;
/** Constant representing a move where an element gets locked. */
const LOCK = 3;
/** Constant representing a move where the sort finishes. */
const END = 4;

/**
 * An object representing a single sorting step.
 * @class
 * @param {element[]} state The state of the list before the swap
 * @param type The type of move to be made - one of COMPARE_ONLY, COMPARE_AND_SWAP, LOCK, END
 * @param {number} element1 The index of the first element the move is applied to (not used for END moves).
 * @param {number} element2 The index of the second element the move is applied to (not used for LOCK moves).
 */
function move(state, type, element1, element2) {
    // the state of the list before the step
    this.state = state;

    // the type of move to be made (as per the constants above)
    // can be one of: COMPARE_ONLY, COMPARE_AND_SWAP, LOCK, and END
    this.type = type;

    // the index of the first element the move is applied to
    this.element1 = element1;

    // the index of the second element the move is applied to
    this.element2 = element2;
}

/** 
 * Used to execute moves in sequence, with the appropriate colour changes
 * @param {move} move The move move object to display
 */
function executeMove(move) {
    createElements(move.state);
    if (move.type === COMPARE_ONLY) {
        $('.displayarea p:nth-child(' + (move.element1 + 1) + ')').addClass('selected');
        $('.displayarea p:nth-child(' + (move.element2 + 1) + ')').addClass('selected');
    } else if (move.type === COMPARE_AND_SWAP) {
        $('.displayarea p:nth-child(' + (move.element1 + 1) + ')').addClass('select-swap');
        $('.displayarea p:nth-child(' + (move.element2 + 1) + ')').addClass('select-swap');
    }
}

/**
 * Displays a move directly - used for stepping backwards. Doesn't use action highlighting.
 * @param move The move object
 */
function displayMove(move) {
    createElements(move.state);
}

/**
 * Creates a deep copy of an object
 * @param {object} object The object to deep copy
 * @returns {object} A deep copy of the object
 */
function copyOf(object) {
    return JSON.parse(JSON.stringify(object));
    // https://stackoverflow.com/questions/122102/what-is-the-most-efficient-way-to-deep-clone-an-object-in-javascript
}

/** 
 * Given a list of numbers, creates a list of moves and steps for a bubblesort.
 * Editing this allows the sorting algorithm to be adjusted.
 * @param {number[]} values A list of numbers to process
 * @returns {move[]} A list of moves required to sort the original list
 * @todo Implement async with timeout?
 */
function buildMoveList(values) {
    /** List of numbers as element objects */
    var elements = values.map(function(x) {
        return new element(x, false);
    });

    /** List of moves required to sort the list */
    var moveList = [];

    /** A flag indicating if the last pass involved no swaps, which means the list has been sorted.
     * Used so a complete pass without swaps can allow early termination (as this means the list is sorted).
     */
    var swappedFlag = false;

    for (var searchTo = elements.length - 1; searchTo > 0; searchTo--) {
        // the following code is executed once for every pass through the list
        swappedFlag = false;

        for (var first = 0; first < searchTo; first++) {
            // the following code is executed once for every non-locked list element
            var second = first + 1;

            if (elements[first].value > elements[second].value) {
                // when a swap is required
                moveList.push(new move(copyOf(elements), COMPARE_AND_SWAP, first, second));
                elements.mySwap(first, second);

                // indicate a swap has occured, preventing early break
                swappedFlag = true;
            } else {
                moveList.push(new move(copyOf(elements), COMPARE_ONLY, first, second));
            }
        }
        // see if any swaps occured - in which case, lock the whole list and break
        if (!swappedFlag) {
            for (var x = 0; x < elements.length; x++) {
                elements[x].locked = true;
            }
            moveList.push(new move(copyOf(elements), END));
            return moveList;
        }


        // lock the last element that hasn't yet been locked
        elements[searchTo].locked = true;
        moveList.push(new move(copyOf(elements), LOCK, searchTo));
    }

    // lock the leftmost element and push the final move onto the move list
    elements[0].locked = true;
    moveList.push(new move(copyOf(elements), END));

    return moveList;
}

/** Current sort step */
var stepNo = 0;
/** ID of the interval used to advance the animation - a value of false indicates no interval in use. */
var intervalID = false;
/** The current list of moves */
var moveList = undefined;

/**
 * Prompts the user for a list of numbers, processes them, builds a move list, prepares for and starts an animation
 */
function getNumsAndRun() {
    // Disables the start button so it can't be clicked again
    $("#start").prop("disabled", true);

    // Pause the animation if it's running
    if (!paused) {
        pause();
    }

    // Ask for numbers from the user
    var numbersIn = prompt("Enter a space-seperated list of numbers");

    // Verifies if numbers have actually been entered
    if ((numbersIn != "") && (numbersIn != undefined)) {

        // Processes the numbers to get them in the right format
        var formattedNumbers = numbersIn.replace(/[^0-9 .]/g, " ") // replace anything that isn't a number, space or . with a space
            .replace(/ +/g, " ") // reduce any consecutive sets of spaces to a single space
            .replace(/^ +/g, "") // remove any spaces on the start of the string
            .replace(/ +$/, "") // remove any spaces on the end of the string
            .split(" "); // split by spaces, leaving a list of numbers only

        // Ensure at least two numbers have been entered
        if (formattedNumbers.length >= 2) {
            // cast all the numbers to floats
            formattedNumbers = formattedNumbers.map(parseFloat);

            // build the move list
            moveList = buildMoveList(formattedNumbers);

            // initalise the animation, update the stats display and start
            stepNo = 0;
            paused = false;
            updateStats();
            run();

            // Ensure the controls are on-screen - they are hidden initially to direct the user to the start button
            $(".pausebox, #speedbox, .stats").slideDown();
            // Change the text on the start button
            $("#start").html("Change numbers");

        } else {
            // Executed when less than two numbers are entered
            alert("List of numbers is too short - enter at least 2 numbers.");
        }

    } else {
        // Executed when numbersIn has no value (e.g. due to the cancel button being pressed)
        alert("List of numbers is too short - enter at least 2 numbers.");
    }

    // Re-enable the start button
    $("#start").prop("disabled", false);
}

/** The interval at which the animation advances one step */
var interval = 1000;

/**
 * Starts the sorting animation.
 */
function run() {
    // run the first step immediately so high intervals don't make it feel unresponsive
    executeMove(moveList[stepNo]);

    // update the display of step number and total steps
    updateStepDisplay();

    // calculate time interval based on slider position
    calculateInterval();

    // clear any previous intervals
    if (intervalID !== false) {
        clearInterval(intervalID);
    }

    // set a repeating event to execute a step
    intervalID = setInterval(advance, interval);

    // hide single stepping controls
    $('.stepbox').slideUp();

    // change pause button text
    $("#pause").html("Pause");
}

/** 
 * Advances the animation by a single step 
 */
function advance() {
    stepNo = (stepNo + 1) % moveList.length;
    executeMove(moveList[stepNo]);
    updateStepDisplay();
}

/**
 * Updates the "Step x/n" display based on the current moveList, and updates the current sort stage info
 */
function updateStepDisplay() {
    $("#steps").html(
        "Step: " +
        (stepNo + 1) + '/' + moveList.length
    );
    // update the info display too
    $('#stepinfo').text(function() {
        switch (moveList[stepNo].type) {
            case COMPARE_ONLY:
                return "Compared, swap not needed";
            case COMPARE_AND_SWAP:
                return "Compared, swap needed";
            case LOCK:
                return "End of pass - this number is in the correct place";
            case END:
                return "List is sorted.";
        }
    });
}

/**
 * Updates the "Comparisons: x" and "Swaps: x" displays
 */
function updateStats() {
    // uses a reduction to find the number of elements with type=COMPARE_ONLY or COMPARE_AND_SWAP
    $('#comps').html(
        "Comparisons: " + moveList.reduce(function(total, x) {
            return (x.type === COMPARE_ONLY || x.type === COMPARE_AND_SWAP) ? total + 1 : total;
        }, 0)
    );
    // uses a reduction to find the number of elements with type=COMPARE_AND_SWAP
    $('#swaps').html(
        "Swaps: " + moveList.reduce(function(total, x) {
            return (x.type === COMPARE_AND_SWAP) ? total + 1 : total;
        }, 0)
    );
}

/** Indicates if the current animation is running */
var paused = false;

/**
 * Toggles the animation between paused and unpaused
 */
function pause() {
    if (paused) {
        stepNo = (stepNo + 1) % moveList.length;
        run();
    } else {
        if (intervalID !== false) {
            clearInterval(intervalID);
        }
        intervalID = false;
        $("#pause").html("Resume");
        $(".stepbox").slideDown();
    }
    paused = !paused;

}

/**
 * Sets interval according to the position of the speed slider and a mapping table
 */
function calculateInterval() {
    // intervals are given in milliseconds and are, in order:
    // 5s, 2.5s, 2s, 1.5s, 1.25s, 1s, 0.75s, 2Hz, 4Hz, 20Hz (where n Hz is n times per second)
    interval = [undefined, 5000, 2500, 2000, 1500, 1250, 1000, 750, 500, 250, 50][$("#speed").val()];
}

/**
 * Single-steps the animation forwards
 */
function stepForward() {
    // Ensure the animation is paused
    if (!paused) {
        pause();
    }

    // Calculates the next stepNo (wrapping round to the beginning if needed)
    stepNo = (stepNo + 1) % moveList.length;

    // Execute the move so the animation is shown
    executeMove(moveList[stepNo]);

    // Update the current step number
    updateStepDisplay();
}

/**
 * Single-steps the animation backwards
 */
function stepBackward() {
    // Ensure the animation is paused
    if (!paused) {
        pause();
    }

    // Calculates the next stepNo (wrapping round to the end if needed)
    stepNo = stepNo - 1;
    if (stepNo <= 0) {
        stepNo = (stepNo + moveList.length) % moveList.length;
    }

    // Show the appropriate move
    executeMove(moveList[stepNo]);

    // Update the current step display
    updateStepDisplay();
}

// configures automatic update of element height when window is resized
$(window).resize(function() {
    if (moveList !== undefined) {
        displayMove(moveList[stepNo]);
    }
});