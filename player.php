<?php 
/* This file is dedicated to the public domain; you may do as you wish with it. */
$v = @(string)$_GET['v'];
$t = @(string)$_GET['t'];
$loop = @(boolean)$_GET['loop'];

if (
    $v[0] !== '/' ||
    preg_match('#^[a-z][a-z0-9+.-]*:#i', $v) ||
    str_contains($v, "\0") ||
    str_contains($v, '..')
) {
    http_response_code(403);
	die('<body style="color:#AF0A0F;font-family:sans-serif;text-align:center;font-size:10pt"><h1>Error</h1><b>Invalid video path!</b>');
}

$params = '?v=' . urlencode($v) . '&amp;t=' . urlencode($t);
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($t); ?></title>
    <link rel="stylesheet" href="stylesheets/webm/playerstyle.css">
    <script src="js/webm-settings.js"></script>
    <script src="js/webm/playersettings.js"></script>
</head>
<body>
    <div id="playerheader">
        <a id="loop0" href="<?php echo $params; ?>&amp;loop=0"<?php if (!$loop) echo ' style="font-weight: bold"'; ?>>[play once]</a>
        <a id="loop1" href="<?php echo $params; ?>&amp;loop=1"<?php if ($loop) echo ' style="font-weight: bold"'; ?>>[loop]</a>
    </div>
    <div id="playercontent">
        <video controls<?php if ($loop) echo ' loop'; ?> src="<?php echo htmlspecialchars($v); ?>">
            Your browser does not support HTML5 video. <a href="<?php echo htmlspecialchars($v); ?>">[Download]</a>
        </video>
    </div>
</body>
</html>
