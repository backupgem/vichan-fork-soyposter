<?php
define('STR_CAPTCHA',		'Incorrect CAPTCHA!');
define('STR_TOKEN',			'Invalid verification token!');
define('STR_FIELD',			'Please fill out every field!');
define('STR_APPEXISTS',		'You\'ve already submitted an application!');

session_start();
function validate_length($value, $min, $max) {
    $length = strlen($value);
    return $length >= $min && $length <= $max;
}
function fancydie($text) {
	die('<!DOCTYPE html>
	<html>
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Error</title>
	<link href="survey.2.css" rel="stylesheet" />
	</head>
	<body>
	<h1>Error</h2>
	<blockquote>'.$text.'</blockquote>
	<form id="janny_application"><input type="submit" onclick="history.back()" value="Back"></form>
	</body>
	</html>');
}

/*
 * MySQL settings
 */
$db_host = 'localhost';
$db_name = 'applications';
$db_user = 'applications';
$db_pass = 'o8284923yt';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    fancydie('MySQL connection failiure');
}


/*
 * Create CSRF token if we don't have one.
 */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


/*
 * CAPTCHA image
 */
if (isset($_GET['action']) && $_GET['action'] === 'get_captcha') {

    // Generate a simple 5-character CAPTCHA.
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $captcha = '';

    for ($i = 0; $i < 5; $i++) {
        $captcha .= $chars[random_int(0, strlen($chars) - 1)];
    }

    // Store the answer in the session.
    $_SESSION['captcha'] = $captcha;

    $width = 74;
    $height = 20;

    $image = imagecreatetruecolor($width, $height);

    $background = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 80, 0, 80);
    $line_color = imagecolorallocate($image, 220, 150, 220);

    imagefill($image, 0, 0, $background);

    // A few lines to make it slightly less trivial to OCR.
    for ($i = 0; $i < 3; $i++) {
        imageline(
            $image,
            random_int(0, $width),
            random_int(0, $height),
            random_int(0, $width),
            random_int(0, $height),
            $line_color
        );
    }

    // Built-in GD font.
    imagestring(
        $image,
        5,
        8,
        2,
        $captcha,
        $text_color
    );

    header('Content-Type: image/png');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    imagepng($image);
    imagedestroy($image);

    exit;
}


/*
 * Handle application submission.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if(@$_SESSION['pow_passed'] !== true || @$_SESSION['pow_ip'] !== $_SERVER['REMOTE_ADDR']) {
        fancydie('<iframe src="/verify/"></iframe>');
}
if (isset($_COOKIE['survey1_submitted'])) {
        fancydie(STR_APPEXISTS);
}
    /*
     * Get IP and user agent.
     */
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';


    /*
     * CSRF check.
     */
    $submitted_token = $_POST['verify'] ?? '';

    if (
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $submitted_token)
    ) {
        http_response_code(403);
        fancydie(STR_TOKEN);
    }


    /*
     * CAPTCHA check.
     */
    $captcha = strtoupper(trim($_POST['captcha'] ?? ''));

    if (
        empty($_SESSION['captcha']) ||
        !hash_equals($_SESSION['captcha'], $captcha)
    ) {
        fancydie(STR_CAPTCHA);
    }

    // Don't allow the same CAPTCHA to be reused.
    unset($_SESSION['captcha']);


    /*
     * Get form fields.
     */
    $username = trim($_POST['janny_username'] ?? '');
    $namefag = trim($_POST['janny_namefag'] ?? '');
    $splinter = trim($_POST['janny_splinter'] ?? '');
    $reason = trim($_POST['janny_reason'] ?? '');

	if (!validate_length($username, 3, 30)) fancydie("Invalid username");
	if (!validate_length($namefag, 3, 100)) fancydie("Not enough text in namefag box");
	if (!validate_length($splinter, 6, 1000)) fancydie("Not enough text in splinter box");
	if (!validate_length($reason, 10, 1000)) fancydie("Not enough text in reason box");
     /*
     * Basic validation.
     */
    if (
        $username === '' ||
        $namefag === '' ||
        $splinter === '' ||
        $reason === ''
    ) {
        fancydie(STR_FIELD);
    }


    /*
     * Check IP before inserting.
     *
     * The UNIQUE constraint on ip_address also protects
     * against two submissions arriving at almost exactly
     * the same time.
     */
    $stmt = $pdo->prepare(
        'SELECT id FROM janny_applications WHERE ip_address = ? LIMIT 1'
    );

    $stmt->execute([$ip]);

    if ($stmt->fetch()) {
        fancydie(STR_APPEXISTS);
    }


    /*
     * Insert application.
     */
    try {

        $stmt = $pdo->prepare(
            'INSERT INTO janny_applications
            (
                ip_address,
                user_agent,
                username,
                namefag,
                splinter,
                reason
            )
            VALUES (?, ?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            $ip,
            $user_agent,
            $username,
            $namefag,
            $splinter,
            $reason
        ]);

    } catch (PDOException $e) {

        if ($e->getCode() === '23000') {
			// if (!isset($_COOKIE['survey1_submitted'])) setcookie("survey1_submitted", "true", time() + (1_DAY * 30));
            fancydie('An application has already been submitted from this IP!');
        }
    error_log(
        'Application INSERT failed: ' .
        $e->getMessage() .
        ' | SQLSTATE: ' . $e->getCode()
    );
        http_response_code(500);
        fancydie('Could not save application.');
    }

/*
} catch (PDOException $e) {

    // Log the database error
    $logMessage = sprintf(
        "[%s] PDOException: %s | Code: %s | File: %s | Line: %d\nStack trace:\n%s\n\n",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getCode(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );

    file_put_contents(
        __DIR__ . '/errorlog.txt',
        $logMessage,
        FILE_APPEND | LOCK_EX
    );

    if ($e->getCode() === '23000') {
        // if (!isset($_COOKIE['survey1_submitted'])) {
        //     setcookie("survey1_submitted", "true", time() + (1_DAY * 30));
        // }

        fancydie('An application has already been submitted from this IP!');
    }

    http_response_code(500);
    fancydie('Could not save application.');
}
*/
    /*
     * Consume the CSRF token so the same form cannot
     * simply be replayed.
     */
    unset($_SESSION['csrf_token']);
	setcookie("survey1_submitted", "true", time() + (86400 * 30));
    echo 'Application submitted successfully!';

    exit;
}


/*
 * If someone visits response.php directly.
 */
http_response_code(405);
header("Location: index.php");
