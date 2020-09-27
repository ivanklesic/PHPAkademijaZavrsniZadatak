<?php


namespace App\Controller;


use App\Core\Controller\AbstractController;
use App\Model\Game;

class HomeController extends AbstractController
{
    private $gameRepository;

    public function __construct()
    {
        $this->gameRepository = new Game\GameRepository();
        parent::__construct();
    }

    public function indexAction()
    {
        $games = $this->gameRepository->getList();

        $data = [
            'games' => $games,
        ];

        $data['session'] = $this->session;
        $this->view->render('index', $data);
    }
}