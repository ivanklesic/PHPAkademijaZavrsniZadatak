<?php


namespace App\Core;


use App\Core\Data\DataObject;
use App\Model\User\UserRepository;

class Session extends DataObject
{
    private static $instance;
    private $userRepository;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->start();
        $this->userRepository = new UserRepository();
    }

    public function __set($key, $value)
    {
        parent::__set($key, $value);
        $_SESSION[$key] = $value;
    }


    public static function getInstance()
    {
        if(self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function setSessionPath()
    {
        $path = BP . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'session';
        session_save_path($path);
        return $this;
    }

    public function start()
    {
        $this->setSessionPath();
        session_start();

        foreach ($_SESSION as $key => $data) {
            $k = 'set' . ucfirst($key);
            $this->$k($data);
        }

        return $this;
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user']);
    }

    public function logout()
    {
        unset($_SESSION['user']);
        session_destroy();
    }

    public function getUser()
    {
        return $this->isLoggedIn() ? $this->userRepository->findOneBy('id', $_SESSION['user']) : null;
    }

    public function setUser($user)
    {
        $_SESSION['user'] = $user->id;
        return;
    }

}