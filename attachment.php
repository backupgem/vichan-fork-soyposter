<?php
include 'inc/bootstrap.php';

$board = $_GET['board'] ?? null;
$filename = $_GET['f'] ?? null;

if (!is_string($board)) error("Invalid board.");
if (!is_string($filename)) error("Invalid filename.");

$boards = listBoards(true);

if (!in_array($board, $boards, true)) {
    error("Invalid board.");
}
if ($board == "janitor") {
	error("Invalid board.");
}
if (
    str_contains($filename, "\0") ||
    str_contains($filename, '..') ||
    str_starts_with($filename, '/') ||
    str_starts_with($filename, '\\')
) {
    error("Invalid filename.");
}

$basePath = realpath($board . '/src');

if ($basePath === false) {
    error("Invalid board.");
}

$path = realpath($basePath . DIRECTORY_SEPARATOR . $filename);

if (
    $path === false ||
    !is_file($path) ||
    !str_starts_with($path, $basePath . DIRECTORY_SEPARATOR)
) {
    error("Invalid file.");
}

$size = filesize($path);

if ($size === false) {
    error("Unable to determine file size.");
}

$mime = mime_content_type($path) ?: 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Accept-Ranges: bytes');
header('Content-Disposition: inline; filename="' . basename($path) . '"');
header('X-Content-Type-Options: nosniff');

$start = 0;
$end = $size - 1;
$status = 200;

/*
 * Handle HTTP Range requests.
 */
if (isset($_SERVER['HTTP_RANGE'])) {
    $range = $_SERVER['HTTP_RANGE'];

    // Only support a single range.
    if (preg_match('/bytes=(\d*)-(\d*)/', $range, $matches)) {
        $rangeStart = $matches[1];
        $rangeEnd = $matches[2];

        if ($rangeStart === '' && $rangeEnd === '') {
            http_response_code(416);
            header("Content-Range: bytes */$size");
            exit;
        }

        if ($rangeStart === '') {
            // Suffix range: bytes=-500
            $length = (int) $rangeEnd;

            if ($length <= 0) {
                http_response_code(416);
                header("Content-Range: bytes */$size");
                exit;
            }

            $start = max(0, $size - $length);
        } else {
            $start = (int) $rangeStart;
        }

        if ($rangeEnd !== '') {
            $end = (int) $rangeEnd;
        }

        // Invalid range.
        if ($start >= $size || $start > $end) {
            http_response_code(416);
            header("Content-Range: bytes */$size");
            exit;
        }

        // Don't allow the requested end past EOF.
        $end = min($end, $size - 1);

        $status = 206;
    }
}

$length = $end - $start + 1;

http_response_code($status);

if ($status === 206) {
    header("Content-Range: bytes $start-$end/$size");
}

header("Content-Length: $length");

$handle = fopen($path, 'rb');

if ($handle === false) {
    error("Unable to open file.");
}

if ($start > 0) {
    fseek($handle, $start);
}

$remaining = $length;

while ($remaining > 0 && !feof($handle)) {
    $chunkSize = min(1024 * 1024, $remaining);

    $data = fread($handle, $chunkSize);

    if ($data === false || $data === '') {
        break;
    }

    echo $data;
    $remaining -= strlen($data);

    // Make sure data gets sent immediately.
    flush();
}

fclose($handle);

if (!isset($_COOKIE['__Host-mod'])) {
userLog(
    "Loaded file /{$board}/src/{$filename}",
    "load_file",
    $board
);
}
exit;
