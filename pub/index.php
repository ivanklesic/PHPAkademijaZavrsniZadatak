<?php

use App\Core\Application;
use App\Core\Routing\Router;
use App\Core\Request\Request;

define('BP', dirname(__DIR__));

spl_autoload_register(function ($class) {
    $class = lcfirst($class);
    $filename = BP . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    if (file_exists($filename)) {
        require_once $filename;
    }
});

$router = new Router(new Request);
$application = new Application($router);

try {
    $response = $application->run();
} catch (\App\Core\Exception\RouterException $e) {
    $response = '<h1>404</h1>';
} catch (\Exception $e) {
    $response = '<h1>500</h1>';
}

echo $response;
