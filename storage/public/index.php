<?php
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$path = '/data/storage.log';

// Ensure log file exists and is writable
if (!file_exists($path)) {
    touch($path);
    chmod($path, 0666);
}

if ($uri === '/log' && $method === 'POST') {
    $data = file_get_contents('php://input');
    file_put_contents($path, $data . "\n", FILE_APPEND);
    http_response_code(201);
    exit;
}

if ($uri === '/log' && $method === 'GET') {
    if (!file_exists($path))
        touch($path);
    
    header('Content-Type: text/plain');
    echo file_get_contents($path);
    exit;
}

if ($uri === '/') {
    echo "Storage service is running.";
    exit;
}

http_response_code(404);
echo "Not Found";
?>