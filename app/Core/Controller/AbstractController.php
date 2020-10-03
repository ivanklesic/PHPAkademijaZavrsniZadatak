<?php


namespace App\Core\Controller;

use App\Core\Config;
use App\Core\Request\Request;
use App\Core\Session;
use App\Core\View;

abstract class AbstractController
{
    protected $view;
    protected $session;
    protected $request;

    public function __construct()
    {
        $this->view = new View();
        $this->session = Session::getInstance();
        $this->request = new Request();
    }

    public function denyAccessUnlessGranted($role)
    {
        $currentUser = $this->session->getCurrentUser();
        if($currentUser)
        {
            if(in_array($role, explode(',',$currentUser->getRoles())))
            {
                return;
            }
        }

        $this->redirectToRoute();
    }

    public function isGranted($role)
    {
        $currentUser = $this->session->getCurrentUser();
        if($currentUser)
        {
            if(in_array($role, explode(',',$currentUser->getRoles())))
            {
                return true;
            }
        }

        return false;
    }


    public function redirectToRoute($route = '/')
    {
        header('Location: ' . Config::prependToURL() . $route);
    }

    public function redirectBack($route)
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}