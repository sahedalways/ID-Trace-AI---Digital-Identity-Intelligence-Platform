<?php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Home page
if ($uri === '/') {
    require __DIR__ . '/index.php';
    return true;
}


$uri = rtrim($uri, '/');


$file = __DIR__ . $uri . '.php';

if (file_exists($file)) {
    require $file;
    return true;
}

return false;
