<?php


namespace App\Controller;


use App\Core\Controller\AbstractController;
use App\Model\Genre;
use App\Core\Validation\GenreValidator;

class GenreController extends AbstractController
{
    private $genreRepository;
    private $genreResource;
    private $genreValidator;

    public function __construct()
    {
        $this->genreRepository = new Genre\GenreRepository();
        $this->genreResource = new Genre\GenreResource();
        $this->genreValidator = new GenreValidator();
        parent::__construct();
    }

    public function createAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $errors = $this->session->errors;
        unset($this->session->errors);

        $this->view->render('genre/create', [
            'edit' => false,
            'errors' => $errors
        ]);
    }

    public function createPostAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $postData = $this->request->getBody();

        $errors = $this->genreValidator->validateForm($postData, 'create');

        if(!empty($errors))
        {
            $this->session->setErrors($errors);
            $this->redirectToRoute('/genre/create');
            exit();
        }

        $result = $this->genreResource->insert($postData);

        if (!$result) {
            return;
        }

        $url = '/genre/list/';
        $this->redirectToRoute($url);
    }

    public function editAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $genre = $this->genreRepository->findOneBy('id', $id);

        if(!$genre)
        {
            return;
        }

        $errors = $this->session->errors;
        unset($this->session->errors);

        $this->view->render('genre/create', [
            'genre' => $genre,
            'edit' => true,
            'errors' => $errors
        ]);
    }

    public function editPostAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $genre = $this->genreRepository->findOneBy('id', $id);

        if(!$genre)
        {
            return;
        }

        $postData = $this->request->getBody();
        $name = $postData['name'];

        if (!$name) {
            return;
        }

        $errors = $this->genreValidator->validateForm($postData);

        if(!empty($errors))
        {
            $this->session->setErrors($errors);
            $this->redirectToRoute('/genre/edit/' . $id);
            exit();
        }

        $result = $this->genreResource->save($id, $postData);

        if (!$result) {
            return;
        }

        $url = '/genre/list/';
        $this->redirectToRoute($url);
    }

    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $genre = $this->genreRepository->findOneBy('id', $id);

        if(!$genre)
        {
            return;
        }

        $result = $this->genreResource->setDeleted($id, 1);

        if (!$result) {
            return;
        }

        $url = '/genre/list/';
        $this->redirectToRoute($url);
    }

    public function restoreAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $genre = $this->genreRepository->findOneBy('id', $id, true);

        if(!$genre)
        {
            return;
        }

        $result = $this->genreResource->setDeleted($id, 0);

        if (!$result) {
            return;
        }

        $url = '/genre/list/';
        $this->redirectToRoute($url);
    }

    public function listAction()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $genres = $this->genreRepository->getList(true);

        $this->view->render('genre/list', [
            'genres' => $genres
        ]);
    }


}