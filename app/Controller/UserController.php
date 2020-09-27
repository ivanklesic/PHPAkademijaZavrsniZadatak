<?php


namespace App\Controller;

use App\Core\Config;
use App\Core\View;
use App\Model\User;


use App\Core\Controller\AbstractController;

class UserController extends AbstractController
{
    private $userRepository;
    private $userResource;

    public function __construct()
    {
        $this->userRepository = new User\UserRepository();
        $this->userResource = new User\UserResource();
        parent::__construct();
    }

    public function loginAction()
    {
        $this->view->render('login', [
            'session' => $this->session
        ]);
    }

    public function loginSubmitAction()
    {
        $session = $this->session;
        if ($session->getUser()) {
            return;
        }

        $postData = $this->request->getBody();
        $email = $postData['email'];
        $password = $postData['pass'];

        if (!$email || !$password) {
            return;
        }

        $user =  $this->userRepository->findOneBy('email', $email);

        if (!$user) {
            return;
        }

        $hash = $user->getPassword();

        if (!password_verify($password, $hash)) {
            return;
        }

        $session->setUser($user);

        $csrfToken = bin2hex(openssl_random_pseudo_bytes(24));
        $session->setCsrfToken($csrfToken);
        header('Location: ' . Config::get('url_local'));
    }

    public function logoutAction()
    {
        $this->session->logout();
        header('Location: ' . Config::get('url_local'));
    }

    public function registerAction()
    {
        if ($this->session->isLoggedIn()) {
            $url = Config::get('url_local');
            header('Location: ' . $url);
        }

        $this->view->render('register', [
            'session' => $this->session
        ]);
    }

    public function registerPostAction()
    {
        $postData = $this->request->getBody();
        $email = $postData['email'];
        $firstname = $postData['firstname'];
        $lastname = $postData['lastname'];
        $password = $postData['pass'];

        if (!$email || !$firstname || !$lastname || !$password) {
            return;
        }

        if($this->userRepository->propertyExists('email', $email)) {
            return;
        }

        $result = $this->userResource->insert($postData);

        if (!$result) {
            return;
        }

        $this->session->logout();
        $url = Config::get('url_local') . 'user/login';
        header('Location: ' . $url);
    }

    public function editAction($id)
    {
        if(!$this->session->isLoggedIn())
        {
            return;
        }

        $user = $this->userRepository->findOneBy('id', $id);

        if(!$user)
        {
            return;
        }

        if($this->session->getUser()->getId() !== $id)
        {
            return;
        }

        $this->view->render('userEdit', [
            'user' => $user,
            'session' => $this->session
        ]);
    }

    public function editPostAction($id)
    {
        if(!$this->session->isLoggedIn())
        {
            return;
        }

        $user = $this->userRepository->findOneBy('id', $id);

        if(!$user)
        {
            return;
        }

        if($this->session->getUser()->getId() !== $id)
        {
            return;
        }

        $postData = $this->request->getBody();
        $email = $postData['email'];
        $firstname = $postData['firstname'];
        $lastname = $postData['lastname'];

        if (!$email || !$firstname || !$lastname)
        {
            return;
        }

        $result = $this->userResource->save($id, $postData);

        if (!$result) {
            return;
        }

        $url = Config::get('url_local') . 'user/details/' . $id;
        header('Location: ' . $url);
    }

    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $this->userRepository->findOneBy('id', $id);

        if(!$user)
        {
            return;
        }

        $result = $this->userResource->setDeleted($id, 1);

        if (!$result) {
            return;
        }

        $url = Config::get('url_local') . 'user/details/' . $id;
        header('Location: ' . $url);
    }

    public function restoreAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $this->userRepository->findOneBy('id', $id);

        if(!$user)
        {
            return;
        }

        $result = $this->userResource->setDeleted($id, 0);

        if (!$result) {
            return;
        }

        $url = Config::get('url_local') . 'user/details/' . $id;
        header('Location: ' . $url);
    }

    public function detailsAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->userRepository->findOneBy('id', $id);

        if(!$user)
        {
            return;
        }

        $this->view->render('userDetails', [
            'game' => $user,
            'session' => $this->session
        ]);
    }

    public function listAction()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $users = $this->isGranted('ROLE_ADMIN') ? $this->userRepository->getList(true) : $this->userRepository->getList();

        if(!$users)
        {
            return;
        }

        $this->view->render('gameDetails', [
            'users' => $users,
            'session' => $this->session
        ]);
    }
}