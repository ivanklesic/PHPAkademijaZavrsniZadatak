<?php


namespace App\Controller;


use App\Core\Controller\AbstractController;
use App\Model\Genre;

class GenreController extends AbstractController
{
    private $genreRepository;
    private $genreResource;

    public function __construct()
    {
        $this->genreRepository = new Genre\GenreRepository();
        $this->genreResource = new Genre\GenreResource();
        parent::__construct();
    }

    public function createAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $this->view->render('genre/create', [
            'edit' => false
        ]);
    }

    public function createPostAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $postData = $this->request->getBody();
        $name = $postData['name'];

        if (!$name) {
            return;
        }

        if($this->genreRepository->propertyExists('name', $name)) {
            return;
        }

        $result = $this->genreResource->insert($postData);

        if (!$result) {
            return;
        }

        $url = '/genre/list/';
        header('Location: ' . $url);
    }

    public function editAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $genre = $this->genreRepository->findOneBy('id', $id);

        if(!$genre)
        {
            return;
        }

        $this->view->render('genre/create', [
            'genre' => $genre,
            'edit' => true
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

        $result = $this->genreResource->save($id, $postData);

        if (!$result) {
            return;
        }

        $url = '/genre/list/';
        header('Location: ' . $url);
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
        header('Location: ' . $url);
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
        header('Location: ' . $url);
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