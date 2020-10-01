<?php


namespace App\Core\Routing;

use App\Core\Exception\RouterException;
use App\Core\Request\RequestInterface;
use Exception;

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

    private function invalidMethodHandler()
    {
        throw new RouterException("{$this->request->serverProtocol} 405 Method Not Allowed");
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function matchRoute($path)
    {
        if(!in_array($this->request->requestMethod, $this->supportedHttpMethods))
        {
            $this->invalidMethodHandler();
        }
        var_dump(1);
        $path = trim($path, '/');
        $path = str_replace(self::URL_SUFFIX, '', $path);
        $parts = $path ? explode('/', $path) : [];

        if (count($parts) > 3) {
            $this->invalidURLHandler($path);
        }
        var_dump(2);
        $controller = ucfirst(strtolower($parts[0] ?? 'home')) . 'Controller';
        $method = strtolower($parts[1] ?? 'index') . 'Action';
        $argument = $parts[2] ?? null;
        var_dump(3);
        $controllerClassName = self::CONTROLLER_NAMESPACE . $controller;
        var_dump($controllerClassName);
        var_dump($method);
        var_dump($argument);

        $this->dispatch($controllerClassName, $method, $argument);
    }

    public function dispatch($controller, $action, $argument = null)
    {
        if(!class_exists($controller))
        {
            $this->missingControllerHandler($controller);
        }

        $controllerObject = new $controller();

        if(!method_exists($controller, $action))
        {
            $this->missingActionHandler($controller, $action);
        }

        if(isset($argument) && is_int((int)$argument))
        {
            try
            {
                return $controllerObject->$action($argument);
            }
            catch(Exception $e)
            {
            }
        }
        return $controllerObject->$action();
    }
}