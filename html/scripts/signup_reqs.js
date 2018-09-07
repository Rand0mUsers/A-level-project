"use strict";

var allowSubmit = false;

function cap_complete() {
    allowSubmit = true;
}

function cap_expired() {
    allowSubmit = false;
}

function check_cap() {
    if (allowSubmit) {
        return true;
    }
    alert("Please complete the reCAPTCHA to continue.");
    return false;
}

window.checkUsername = function () {
    var username = document.getElementById('username').value;
    if (
        username !== "" && // don't show anything if the box is blank
        /^[a-zA-z0-9_]{4,}$/.test(username) // username validation: 4 or more letters (upper/lowercase), numbers or underscores
    ) {
        document.getElementById('namealert').style.display = "none";
    } else {
        document.getElementById('namealert').style.display = "initial";
    }
}

function passwordRequirementCheck() {
    var pwd = document.getElementById('password').value;
    if (pwd !== "") {
        var message = "";
        if (!/[a-z]/.test(pwd)) {
            message += "\n<li>Requires lowercase</li>";
        }
        if (!/[A-Z]/.test(pwd)) {
            message += "\n<li>Requires uppercase</li>";
        }
        if (!/[0-9]/.test(pwd)) {
            message += "\n<li>Requires a number</li>";
        }
        if (pwd.length < 8) {
            message += "\n<li>Must be at least 8 characters</li>";
        }
        if (message != "") {
            message = "<p>Password requirements not met:</p>\n<ul>" + message + "</ul>";
        }
        document.getElementById('passreqs').innerHTML = message;
    } else {
        document.getElementById('passreqs').innerHTML = "";
    }
}

function passwordCheckStrict() {
    if ((document.getElementById('password').value != document.getElementById('verifypassword').value) && (document.getElementById('verifypassword').value != "")) {
        // warn only if the passwords don't match AND the 'verify password' field isn't blank
        document.getElementById('matchalert').style.display = "initial";
    } else {
        document.getElementById('matchalert').style.display = "none";
    }
}

// holds the interval ID of the timeout used to display a "passwords don't match" warning 1s after typing
var passwordTestTimeOut = false;

window.comparePasswords = function () {
    var pwdbox = document.getElementById('password'),
        verpwdbox = document.getElementById('verifypassword'),
        pwd = pwdbox.value,
        verpwd = verpwdbox.value;

    if (pwd === verpwd) {
        // complete match
        if (passwordTestTimeOut !== false) {
            clearTimeout(passwordTestTimeOut);
        }
        document.getElementById('matchalert').style.display = "none";
    } else if (pwd.startsWith(verpwd)) {
        // password correct so far - remind in 3 seconds' time
        if (passwordTestTimeOut !== false) {
            clearTimeout(passwordTestTimeOut);
        }
        passwordTestTimeOut = setTimeout(passwordCheckStrict, 1000);
        document.getElementById('matchalert').style.display = "none";
    } else if (verpwd != "") {
        // passwords do not match at all
        if (passwordTestTimeOut !== false) {
            clearTimeout(passwordTestTimeOut);
        }
        document.getElementById('matchalert').style.display = "initial";
    }

}