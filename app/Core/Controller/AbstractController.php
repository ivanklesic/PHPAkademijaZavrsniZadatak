<?php


namespace App\Core\Controller;

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

        header('Location: /');
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

    public function redirect($path = '/')
    {

    }
}