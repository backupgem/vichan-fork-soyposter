<?php

require_once 'inc/bootstrap.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
	$is_https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
	session_set_cookie_params([
		'path' => '/',
		'secure' => $is_https,
		'httponly' => true,
		'samesite' => 'Lax'
	]);
	session_start();
}

function account_render(array $body_options = []): void {
	global $config, $mod;

	echo Element($config['file_page_template'], [
		'title' => 'Account',
		'config' => $config,
		'boardlist' => createBoardlist(isset($mod) ? $mod : false),
		'body' => Element(
			!empty($body_options['logged_in']) ? 'user2.html' : 'user.html',
			$body_options
		)
	]);
}

function account_csrf_token(): string {
	if (!isset($_SESSION['account_csrf_token']) || !is_string($_SESSION['account_csrf_token'])) {
		$_SESSION['account_csrf_token'] = bin2hex(random_bytes(32));
	}

	return $_SESSION['account_csrf_token'];
}

function account_check_csrf(): void {
	if (!isset($_POST['token'], $_SESSION['account_csrf_token']) ||
		!is_string($_POST['token']) ||
		!hash_equals($_SESSION['account_csrf_token'], $_POST['token'])) {
		error('Invalid security token. Please go back and try again.');
	}
}

account_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
	account_check_csrf();
	$logged_out_account_id = isset($_SESSION['account_id']) ? (int)$_SESSION['account_id'] : null;
	unset($_SESSION['account_id']);
	session_regenerate_id(true);
	$_SESSION['account_csrf_token'] = bin2hex(random_bytes(32));
	if ($logged_out_account_id !== null)
		modLog('User account #' . $logged_out_account_id . ' logged out');
	account_render(['logged_out' => true, 'token' => account_csrf_token()]);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['password'])) {
	account_check_csrf();

	if (!is_string($_POST['user_id']) || !ctype_digit($_POST['user_id']) ||
		!is_string($_POST['password']) || strlen($_POST['password']) > 4096) {
		account_render([
			'error' => 'Invalid user ID or password.',
			'token' => account_csrf_token()
		]);
		exit;
	}

	$user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT, [
		'options' => ['min_range' => 1]
	]);

	if ($user_id === false) {
		account_render([
			'error' => 'Invalid user ID or password.',
			'token' => account_csrf_token()
		]);
		exit;
	}

	$query = prepare('SELECT `id`, `password` FROM ``user_accounts`` WHERE `id` = :id LIMIT 1');
	$query->bindValue(':id', $user_id, PDO::PARAM_INT);
	$query->execute() or error(db_error($query));
	$account = $query->fetch(PDO::FETCH_ASSOC);

	if (!$account || !password_verify($_POST['password'], $account['password'])) {
		account_render([
			'error' => 'Invalid user ID or password.',
			'token' => account_csrf_token()
		]);
		exit;
	}

	if (password_needs_rehash($account['password'], PASSWORD_DEFAULT)) {
		$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
		if ($password_hash === false)
			error('Could not securely update the account password hash.');

		$query = prepare('UPDATE ``user_accounts`` SET `password` = :password WHERE `id` = :id');
		$query->bindValue(':password', $password_hash, PDO::PARAM_STR);
		$query->bindValue(':id', $user_id, PDO::PARAM_INT);
		$query->execute() or error(db_error($query));
	}

	session_regenerate_id(true);
	$_SESSION['account_id'] = (int)$account['id'];
	$_SESSION['account_csrf_token'] = bin2hex(random_bytes(32));
	modLog('User account #' . (int)$account['id'] . ' logged in');
	account_render([
		'logged_in' => true,
		'user_id' => (int)$account['id'],
		'token' => account_csrf_token()
	]);
	exit;
}

if (isset($_SESSION['account_id'])) {
	$query = prepare('SELECT `id` FROM ``user_accounts`` WHERE `id` = :id LIMIT 1');
	$query->bindValue(':id', (int)$_SESSION['account_id'], PDO::PARAM_INT);
	$query->execute() or error(db_error($query));

	if (!$query->fetchColumn()) {
		unset($_SESSION['account_id']);
		session_regenerate_id(true);
	}
}

if (isset($_SESSION['account_id'])) {
	account_render([
		'logged_in' => true,
		'user_id' => (int)$_SESSION['account_id'],
		'token' => account_csrf_token()
	]);
	exit;
}

account_render(['token' => account_csrf_token()]);

