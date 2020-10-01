<?php


namespace App;
use App\Core\Routing\RouterInterface;


class Application
{
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function run()
    {
        return $this->router->matchRoute($this->router->getRequest()->pathInfo ?? '/');
    }

}