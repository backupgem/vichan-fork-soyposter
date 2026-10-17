<?php
require 'inc/bootstrap.php';
$flag = (string)$_GET['flag'];
if (!is_string($flag))
	error("Invalid flag.");

if (!preg_match('/^[A-Za-z0-9]{2}$/', $flag))
	error("Invalid flag.");

$flag = strtolower($flag);

if (str_starts_with($flag, '1'))
	$file = __DIR__ . "/custom_flags/$flag.png";
else
	$file = __DIR__ . "/static/flags/$flag.png";

if (!is_file($file))
    error("Flag not found.");

header('Content-Type: image/png');
header('Content-Length: ' . filesize($file));
header('Cache-Control: public, max-age=31536000, immutable');

readfile($file);
exit;
