<?php

namespace App\Controller;

use App\Core\Controller\AbstractController;
use App\Model\Game;
use App\Model\Genre;

class HomeController extends AbstractController
{
    private $gameRepository;
    private $genreRepository;

    public function __construct()
    {
        $this->gameRepository = new Game\GameRepository();
        $this->genreRepository = new Genre\GenreRepository();
        parent::__construct();
    }

    public function indexAction($genreID = null)
    {
        if($this->session->isLoggedIn())
        {
            $games = $genreID ? $this->gameRepository->findByGenre($genreID) : $this->gameRepository->getList();

            $data = [
                'games' => $games,
                'genres' => $this->genreRepository->getList(),
            ];

            $this->view->render('index', $data);
        }
        else
        {
            $url = '/user/login/';
            $this->redirectToRoute($url);
        }
    }
}