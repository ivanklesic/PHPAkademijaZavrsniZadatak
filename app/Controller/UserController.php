<?php


namespace App\Controller;


use App\Model\User;
use App\Model\Genre;

use App\Core\Controller\AbstractController;

class UserController extends AbstractController
{
    private $userRepository;
    private $genreRepository;
    private $userResource;

    public function __construct()
    {
        $this->userRepository = new User\UserRepository();
        $this->genreRepository = new Genre\GenreRepository();
        $this->userResource = new User\UserResource();
        parent::__construct();
    }

    public function loginAction()
    {
        if ($this->session->isLoggedIn()) {
            return;
        }

        $this->view->render('user/login', [
        ]);
    }

    public function loginPostAction()
    {
        if ($this->session->isLoggedIn()) {
            return;
        }

        $postData = $this->request->getBody();
        $email = $postData['email'] ?? null;
        $password = $postData['pass'] ?? null;

        if (!$email || !$password) {
            return;
        }

        $user =  $this->userRepository->findOneBy('email', $email);

        if (!$user) {
            return;
        }

        if ($user->deleted) {
            return;
        }

        $hash = $user->getPassword();

        if (!password_verify($password, $hash)) {
            return;
        }

        $this->session->setUser($user);

        $csrfToken = bin2hex(random_bytes(32));
        $this->session->setCsrfToken($csrfToken);
        header('Location: /');
    }

    public function logoutAction()
    {
        $this->session->logout();
        header('Location: /');
    }

    public function registerAction()
    {
        if ($this->session->isLoggedIn()) {
            header('Location: /');
        }

        $this->view->render('user/register', [
            'genres' => $this->genreRepository->getList(),
            'edit' => false
        ]);
    }

    public function registerPostAction()
    {
        if ($this->session->isLoggedIn()) {
            header('Location: /');
        }

        $postData = $this->request->getBody();
        $email = $postData['email'];
        $firstname = $postData['firstname'];
        $lastname = $postData['lastname'];
        $password = $postData['pass'];
        $password2 = $postData['pass-r'];

        if (!$email || !$firstname || !$lastname || !$password) {
            return;
        }

        if ($password != $password2) {
            return;
        }

        if($this->userRepository->propertyExists('email', $email)) {
            return;
        }

        if(!is_dir('upload')){
            mkdir('upload');
        }

        if(isset($_FILES['file'])){
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileMimeType = finfo_file($fileInfo, $_FILES['file']['tmp_name']);
            if (($fileMimeType == 'image/jpeg') && $_FILES['file']['size'] < 100 * 1024){
                move_uploaded_file($_FILES['file']['tmp_name'], 'upload' . DIRECTORY_SEPARATOR . $email . '.jpeg');
                $postData['imageurl'] = $email . '.jpeg';
            }
            finfo_close($fileInfo);
        }

        $result = $this->userResource->insert($postData);

        if (!$result) {
            return;
        }

        $user = $this->userRepository->findOneBy('email',$email);

        foreach($postData['genres'] as $genreID)
        {
            $result = $this->userResource->insertGenre($genreID, $user->getId());
            if (!$result) {
                return;
            }
        }

        $url = '/user/login';
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

        if($this->session->getCurrentUser()->getId() !== $id)
        {
            return;
        }

        $this->view->render('user/register', [
            'user' => $user,
            'edit' => true,
            'genres' => $this->genreRepository->getList(),
            'userGenres' => $this->userRepository->findGenreIDs($user->getId())
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

        if($this->session->getCurrentUser()->getId() !== $id)
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

        if(isset($_FILES['profileimg'])){
            if($user->getImageUrl())
            {
                unlink('upload/' . $user->getImageUrl());
            }
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileMimeType = finfo_file($fileInfo, $_FILES['profileimg']['tmp_name']);
            if (($fileMimeType == 'image/jpeg') && $_FILES['profileimg']['size'] < 100 * 1024){
                move_uploaded_file($_FILES['profileimg']['tmp_name'], 'upload' . DIRECTORY_SEPARATOR . $email . '.jpeg');
                $postData['imageurl'] = $email . '.jpeg';
            }
            finfo_close($fileInfo);
        }

        $result = $this->userResource->save($id, $postData);

        if (!$result) {
            return;
        }

        $this->userResource->resetGenres($user->getId());
        foreach($postData['genres'] as $genreID)
        {
            $result = $this->userResource->insertGenre($genreID, $user->getId());
            if (!$result) {
                return;
            }
        }

        header('Location: /');
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

        $url = '/user/list/' . $id;
        header('Location: ' . $url);
    }

    public function restoreAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $this->userRepository->findOneBy('id', $id, true);

        if(!$user)
        {
            return;
        }

        $result = $this->userResource->setDeleted($id, 0);

        if (!$result) {
            return;
        }

        $url = '/user/list/' . $id;
        header('Location: ' . $url);
    }

    public function listAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $this->userRepository->getList(true);

        $this->view->render('user/list', [
            'users' => $users
        ]);
    }
}