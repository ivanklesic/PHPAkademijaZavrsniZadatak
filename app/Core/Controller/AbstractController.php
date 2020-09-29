<?php


namespace App\Core\Controller;


use App\Core\Config;
use App\Core\Request\Request;
use App\Core\Session;
use App\Core\View;

class AbstractController
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
        if($this->session->getCurrentUser())
        {
            if(in_array($role, explode(',',$this->session->getCurrentUser()->getRoles())))
            {
                return;
            }
        }

        header('Location: /');
    }

    public function isGranted($role)
    {
        if($this->session->getCurrentUser())
        {
            if(in_array($role, explode(',',$this->session->getCurrentUser()->getRoles())))
            {
                return true;
            }
        }

        return false;
    }


}