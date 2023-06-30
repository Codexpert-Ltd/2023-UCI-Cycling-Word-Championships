<?php
require "../start.php";
use Src\Post;
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if ($uri[1] !== 'api') {
    header("HTTP/1.1 404 Not Found");
    print $uri[2];
    exit();
}

$postId = null;
if (isset($uri[2])) {
    $postId = (int)$uri[2];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];
$controller = new Post($dbConnection, $requestMethod, $postId);
$controller->processRequest();
