<?php
/*
# https redirect
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $location);
    exit;
}
*/

// Timezone
date_default_timezone_set('Europe/Warsaw'); // Etc/UTC or Europe/Warsaw

// Charset
ini_set('default_charset', 'utf-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_regex_encoding('UTF-8');

// Execution time (0 - unlimited)
set_time_limit(600);

// Show erors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

// Session lifetimes
ini_set('session.gc_probability', 0); // Disable garbage collector
ini_set('session.gc_divisor', 1);
ini_set('session.gc_maxlifetime', 86400); // 86400 => 1 day
// Session dir: /var/lib/php/sessions
// ini_set('session.save_path', session_save_path());

// Session cookie
session_set_cookie_params([
    'lifetime' => 0, // unlimited cookie time
    'path' => '/',
    'domain' => '.'.$_SERVER["HTTP_HOST"],
    'secure' => isset($_SERVER["HTTPS"]), // Set to 1 for secure only
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Composer autoload
$autoload = __DIR__.'/../vendor/autoload.php';

if(!file_exists($autoload)) {
    die('Create composer autoload first! Install composer and run command: "composer update --no-dev" or "composer dump-autoload -o --no-dev" in document root directory.');
} else {
    require_once $autoload;
}

// Session always after autoload
session_start();

// Routes, settings
require('../routes/web.php');
?>