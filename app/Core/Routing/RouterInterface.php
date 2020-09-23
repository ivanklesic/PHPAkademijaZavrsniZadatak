<?php


namespace App\Core\Routing;


interface RouterInterface
{
    public function matchRoute($path);
    public function dispatch($controller, $method, $argument);
}