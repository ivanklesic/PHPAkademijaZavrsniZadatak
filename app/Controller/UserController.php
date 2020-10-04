<?php


namespace App\Controller;

use App\Model\User;
use App\Model\Genre;
use App\Core\Validation\UserValidator;
use App\Core\Validation\SpecValidator;

use App\Core\Controller\AbstractController;

class UserController extends AbstractController
{
    private $userRepository;
    private $genreRepository;
    private $userResource;
    private $userValidator;
    private $specValidator;

    public function __construct()
    {
        $this->userRepository = new User\UserRepository();
        $this->genreRepository = new Genre\GenreRepository();
        $this->userResource = new User\UserResource();
        $this->userValidator = new UserValidator();
        $this->specValidator = new SpecValidator();
        parent::__construct();
    }

    public function loginAction()
    {
        if ($this->session->isLoggedIn()) {
            $this->session->logout();
        }

        $errors = $this->session->errors;
        unset($this->session->errors);

        $this->view->render('user/login', [
            'errors' => $errors
        ]);
    }

    public function loginPostAction()
    {
        if ($this->session->isLoggedIn()) {
            $this->session->logout();
        }

        $postData = $this->request->getBody();

        $user =  $this->userRepository->findOneBy('email', $postData['email'], true);

        if(!$user)
        {
            return;
        }

        $errors = $this->userValidator->validateLogin($postData, $user);

        if(!empty($errors))
        {
            $this->session->setErrors($errors);
            $this->redirectToRoute('/user/login');
            exit();
        }

        $this->session->setUser($user);

        $csrfToken = bin2hex(random_bytes(32));
        $this->session->setCsrfToken($csrfToken);
        $this->redirectToRoute();
    }

    public function logoutAction()
    {
        $this->session->logout();
        $this->redirectToRoute();
    }

    public function registerAction()
    {
        $admin = $this->isGranted('ROLE_ADMIN');

        if ($this->session->isLoggedIn() && !$admin) {
            $this->redirectToRoute();
        }

        $errors = $this->session->errors;
        unset($this->session->errors);


        $this->view->render('user/register', [
            'genres' => $this->genreRepository->getList(),
            'edit' => false,
            'errors' => $errors
        ]);
    }

    public function registerPostAction()
    {
        $admin = $this->isGranted('ROLE_ADMIN');

        if ($this->session->isLoggedIn() && !$admin) {
            $this->redirectToRoute();
        }

        var_dump(1);

        $postData = $this->request->getBody();
        $email = $postData['email'];

        $errors = array_merge($this->userValidator->validateRegister($postData, 'register'), $this->specValidator->validateRegister($postData));

        if(!empty($errors))
        {
            $this->session->setErrors($errors);
            $this->redirectToRoute('/user/register');
            exit();
        }
        var_dump(2);

        if(!is_dir('upload')){
            mkdir('upload');
        }

        if(isset($_FILES['profileimg']) && $_FILES['profileimg']['name'] !== ""){
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileMimeType = finfo_file($fileInfo, $_FILES['profileimg']['tmp_name']);
            if (($fileMimeType == 'image/jpeg') && $_FILES['profileimg']['size'] < 100 * 1024){
                move_uploaded_file($_FILES['profileimg']['tmp_name'], 'upload' . DIRECTORY_SEPARATOR . $email . '.jpeg');
                $postData['imageurl'] = $email . '.jpeg';
            }
            finfo_close($fileInfo);
        }
        var_dump(3);

        $result = $this->userResource->insert($postData, $admin);

        if (!$result) {
            return;
        }
        var_dump(4);

        $user = $this->userRepository->findOneBy('email',$email);

        if(isset($postData['genres']) && !empty($postData['genres']))
        {
            foreach($postData['genres'] as $genreID)
            {
                $result = $this->userResource->insertGenre($genreID, $user->getId());
                if (!$result) {
                    return;
                }
            }
        }

        $url = $admin ? '/user/list' : '/user/login';
        $this->redirectToRoute($url);
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

        $errors = $this->session->errors;
        unset($this->session->errors);

        $this->view->render('user/register', [
            'user' => $user,
            'edit' => true,
            'genres' => $this->genreRepository->getList(),
            'userGenres' => $this->userRepository->findGenreIDs($user->getId()),
            'errors' => $errors
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

        $errors = array_merge($this->userValidator->validateRegister($postData), $this->specValidator->validateRegister($postData));

        if(!empty($errors))
        {
            $this->session->setErrors($errors);
            $this->redirectToRoute('/user/edit/' . $id);
            exit();
        }

        if(isset($_FILES['profileimg']) && $_FILES['profileimg']['name'] !== ""){
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

        if(isset($postData['genres'])  && !empty($postData['genres']))
        {
            foreach($postData['genres'] as $genreID)
            {
                $result = $this->userResource->insertGenre($genreID, $user->getId());
                if (!$result) {
                    return;
                }
            }
        }

        $this->redirectToRoute();
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
        $this->redirectToRoute($url);
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
        $this->redirectToRoute($url);
    }

    public function listAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $this->userRepository->getList(true);

        $this->view->render('user/list', [
            'users' => $users
        ]);
    }

    public function resetAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->userRepository->findOneBy('id',$id);

        if(!$user)
        {
            return;
        }

        $errors = $this->session->errors;
        unset($this->session->errors);

        $this->view->render('user/reset', [
            'user' => $user,
            'errors' => $errors
        ]);
    }

    public function resetPostAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $postData = $this->request->getBody();
        $passwordNew = $postData['pass'];

        $user = $this->userRepository->findOneBy('id',$id);

        if(!$user)
        {
            return;
        }

        $errors = $this->userValidator->validateReset($postData, $user);

        if(!empty($errors))
        {
            $this->session->setErrors($errors);
            $this->redirectToRoute('/user/reset/' . $id);
            exit();
        }

        $result = $this->userResource->resetPassword($id, $passwordNew);

        if (!$result) {
            return;
        }

        $this->session->logout();
        $this->redirectToRoute();
    }

}