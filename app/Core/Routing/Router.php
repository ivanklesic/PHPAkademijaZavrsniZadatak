<?php


namespace App\Core\Routing;

use App\Core\Exception\RouterException;
use App\Core\Request\RequestInterface;

class Router implements RouterInterface
{
    private $request;
    public const URL_SUFFIX = '.html';
    public const CONTROLLER_NAMESPACE = '\\App\\Controller\\';
    private $supportedHttpMethods = array(
        "GET",
        "POST"
    );
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    private function invalidURLHandler($path)
    {
        throw new RouterException("{$path} is not a valid URL");
    }
    private function missingControllerHandler($controller)
    {
        throw new RouterException("{$controller} doesn't exist");
    }
    private function missingActionHandler($controller, $action)
    {
        throw new RouterException("{$action} doesn't exist in {$controller}");
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function matchRoute($path)
    {
        $path = trim($path, '/');
        $path = str_replace(self::URL_SUFFIX, '', $path);
        $parts = $path ? explode('/', $path) : [];

        if (count($parts) > 3) {
            $this->invalidURLHandler($path);
        }

        $controller = ucfirst(strtolower($parts[0] ?? 'home')) . 'Controller';
        $method = strtolower($parts[1] ?? 'index') . 'Action';
        $argument = $parts[2] ?? null;

        $controllerClassName = self::CONTROLLER_NAMESPACE . $controller;

        $this->dispatch($controllerClassName, $method, $argument);
    }

    public function dispatch($controller, $action, $argument)
    {
        if(!class_exists($controller))
        {
            $this->missingControllerHandler($controller);
        }
        if(!method_exists($controller, $action))
        {
            $this->missingActionHandler($controller, $action);
        }
        $controllerObject = new $controller();

        return $argument ? $controllerObject->$action($argument) : $controllerObject->$action();
    }
}