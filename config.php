<?php

// Configuration values for the database and CAPTCHA validation
// NOTE: THE KEYS BELOW HAVE BEEN ADJUSTED TO THE TEST KEYS
//   THIS SHOULD MEAN THEY WORK WITHOUT VALIDATION
$config = [
    'normal_captcha_secret' => '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI',
    'invisible_captcha_secret' => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe',
    'database_user' => 'user',
    'database_pass' => ''
];


// Creates & returns a database connection object using sensible default settings
function get_PDO() {
    global $config;
    return new PDO('mysql:host=localhost;dbname=d_one;charset=utf8mb4',
        $config['database_user'],
        $config['database_pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
}

// Validates a Google reCAPTCHA response, by sending a POST request to Google servers
// and interpreting the response.
// Returns TRUE only if the response was correct, FALSE otherwise.
// Throws an exception if something goes wrong.
function validate_g_captcha($secret, $response) {
    global $config;

    // Configures data and options for POST request
    $url = "https://www.google.com/recaptcha/api/siteverify";
    $data = [
        'secret' => $secret,
        'response' => $response
    ];
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    // Execute the request
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    // $result is false if the request fails - throws exception
    if ($result === FALSE) {
        throw new Exception("Connection error");
    }

    // Decode the JSON data returned and return true only if CAPTCHA OK
    $result = json_decode($result, true);
    if ($result['success']) {
        return TRUE;
    }
    return FALSE;
}

// Validates a login given the username, password and account type
// Returns TRUE when the login is valid
// Returns FALSE when the login is invalid (wrong username OR password)
// Throws an exception if the account type constant is invalid
const ACCOUNT_TEACHER = 1;
const ACCOUNT_STUDENT = 2;

function validate_login($username, $password, $accounttype) {

    // DB connection object
    $pdo = get_PDO();

    // Set query string according to account type
    if ($accounttype === ACCOUNT_TEACHER) {
        $stmt = $pdo->prepare("SELECT * FROM Teacher WHERE Username=:username");
    } elseif ($accounttype === ACCOUNT_STUDENT) {
        $stmt = $pdo->prepare("SELECT * FROM Student WHERE Username=:username");
    } else {
        throw new Exception("Invalid account type");
        return FALSE;
    }

    // Execute query and retrieve data
    $stmt->execute([
        'username' => strtolower($username)
    ]);
    $data = $stmt->fetch();

    // Check password isn't blank
    if (isset($password) and $password !== "") {

        // Check if user-supplied password is correct
        if (password_verify($password, $data['PasswordHash'])) {

            // Update password hash if needed
            if (password_needs_rehash($data['PasswordHash'], PASSWORD_DEFAULT)) {

                // Set statement according to account type
                if ($accounttype === ACCOUNT_TEACHER) {
                    $updatestmt = $pdo->prepare("UPDATE Teacher SET PasswordHash=:passwordhash WHERE Username=:username");
                } elseif ($accounttype === ACCOUNT_STUDENT) {
                    $updatestmt = $pdo->prepare("UPDATE Student SET PasswordHash=:passwordhash WHERE Username=:username");
                }
                // Execute password hash update
                $updatestmt->execute([
                    'username' => strtolower($username),
                    'passwordhash' => password_hash($password, PASSWORD_DEFAULT)
                ]);
            }
            // Login valid
            return TRUE;
        } else {
            // Username and/or password wrong
            return FALSE;
        }
    } else {
        // No password set or provided
        return FALSE;
    }
}

?>
