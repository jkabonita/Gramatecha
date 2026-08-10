<?php

$publicPath = getcwd() . '/public';

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Allow direct access to actual FILES (not directories) in the public folder.
// Directories (e.g. public/presentations/) must still be handled by Laravel,
// otherwise PHP's built-in server returns 404 for them.
$fullPath = $publicPath . $uri;

if ($uri !== '/' && file_exists($fullPath) && !is_dir($fullPath)) {
    return false; // serve static file as-is
}

// Everything else (including URIs that map to real directories) goes to Laravel.
$formattedDateTime = date('D M j H:i:s Y');
$requestMethod = $_SERVER['REQUEST_METHOD'];
$remoteAddress = $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['REMOTE_PORT'];

file_put_contents('php://stdout', "[$formattedDateTime] $remoteAddress [$requestMethod] URI: $uri\n");

chdir($publicPath);
require_once $publicPath . '/index.php';
