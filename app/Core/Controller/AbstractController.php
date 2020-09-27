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
        if($this->session->getUser())
        {
            if(in_array($role, json_decode($this->session->getUser()->getRoles())))
            {
                return;
            }
        }

        $url = Config::get('url_local') . 'home/index';
        header('Location: ' . $url);
    }

    public function isGranted($role)
    {
        if($this->session->getUser())
        {
            if(in_array($role, $this->session->getUser()->getRoles()))
            {
                return true;
            }
        }

        return false;
    }


}