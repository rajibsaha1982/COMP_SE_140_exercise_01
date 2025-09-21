<?php
$uri    = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
 
function nowUTC() {
    return gmdate('Y-m-d\TH:i:s\Z');
}
 
function uptimeHours() {
    $u = floatval(explode(' ', file_get_contents('/proc/uptime'))[0]);
    return number_format($u / 3600, 2);
}
 
function freeMB() {
    return round(disk_free_space('/') / 1024 / 1024);
}
 
function postToStorage($rec) {
    $ctx = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: text/plain',
            'content' => $rec
        ]
    ]);
    file_get_contents('http://storage:8000/log', false, $ctx);
}
 
function appendToVolume($rec) {
    if (!file_exists('/vstorage/storage.txt')) {
        touch('/vstorage/storage.txt');
        chmod('/vstorage/storage.txt', 0666);
    }
    file_put_contents('/vstorage/storage.txt', $rec . "\n", FILE_APPEND);
}
 
if ($uri === '/') {
    header('Content-Type: text/plain');
    echo "Service1 is running. Available endpoints:\n";
    echo "- GET /status : Show system status\n";
    echo "- GET /log : Show logs from storage\n";
    exit;
}

if ($uri === '/status' && $method === 'GET') {
    header('Content-Type: text/plain');
    $r1 = nowUTC() . ": uptime " . uptimeHours() . " hours, free disk in root: " . freeMB() . " MBytes";
    postToStorage($r1);
    appendToVolume($r1);
    
    try {
        $r2 = @file_get_contents('http://service2:8080/');
        if ($r2 === false) {
            $r2 = "Service2 unavailable";
        }
    } catch (Exception $e) {
        $r2 = "Service2 error: " . $e->getMessage();
    }
    
    echo $r1 . "\n" . $r2;
    exit;
}
 
if ($uri === '/log' && $method === 'GET') {
    $log = file_get_contents('http://storage:8000/log');
    header('Content-Type: text/plain');
    echo $log;
    exit;
}
 
http_response_code(404);
echo "Not Found";
?>