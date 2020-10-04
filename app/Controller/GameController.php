<?php


namespace App\Controller;

use App\Core\Controller\AbstractController;
use App\Core\Validation\SpecValidator;
use App\Model\Game;
use App\Model\Genre;
use App\Core\Curl;
use App\Core\Validation\GameValidator;

class GameController extends AbstractController
{
    private $gameRepository;
    private $genreRepository;
    private $gameResource;
    private $gameValidator;
    private $specValidator;

    public function __construct()
    {
        $this->gameRepository = new Game\GameRepository();
        $this->genreRepository = new Genre\GenreRepository();
        $this->gameResource = new Game\GameResource();
        $this->gameValidator = new GameValidator();
        $this->specValidator = new SpecValidator();
        parent::__construct();
    }

    public function createAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $errors = $this->session->errors;
        unset($this->session->errors);

        $this->view->render('game/create', [
            'genres' => $this->genreRepository->getList(),
            'edit' => false,
            'errors' => $errors
        ]);
    }

    public function createPostAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $postData = $this->request->getBody();
        $name = $postData['name'];

        $errors = array_merge($this->gameValidator->validateForm($postData, 'create'), $this->specValidator->validateRegister($postData, 'game'));

        if(!empty($errors))
        {
            $this->session->setErrors($errors);
            $this->redirectToRoute('/game/create');
            exit();
        }

        $result = $this->gameResource->insert($postData);

        if (!$result) {
            return;
        }

        $game = $this->gameRepository->findOneBy('name',$name);

        foreach($postData['genres'] as $genreID)
        {
            $result = $this->gameResource->insertGenre($genreID, $game->getId());
            if (!$result) {
                return;
            }
        }

        $url = '/game/list/';
        $this->redirectToRoute($url);
    }

    public function editAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $game = $this->gameRepository->findOneBy('id', $id);

        if(!$game)
        {
            return;
        }

        $errors = $this->session->errors;
        unset($this->session->errors);

        $this->view->render('game/create', [
            'game' => $game,
            'genres' => $this->genreRepository->getList(),
            'edit' => true,
            'gameGenres' => $this->gameRepository->findGenreIDs($game->getId()),
            'errors' => $errors
        ]);
    }

    public function editPostAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $game = $this->gameRepository->findOneBy('id', $id);

        if(!$game)
        {
            return;
        }

        $postData = $this->request->getBody();

        $errors = array_merge($this->gameValidator->validateForm($postData), $this->specValidator->validateRegister($postData, 'game'));

        if(!empty($errors))
        {
            $this->session->setErrors($errors);
            $this->redirectToRoute('/game/edit/' . $id);
            exit();
        }

        $result = $this->gameResource->save($id, $postData);

        if (!$result) {
            return;
        }

        $this->gameResource->resetGenres($game->getId());

        foreach($postData['genres'] as $genreID)
        {
            $result = $this->gameResource->insertGenre($genreID, $game->getId());
            if (!$result) {
                return;
            }
        }

        $url = '/game/list/';
        $this->redirectToRoute($url);
    }

    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $game = $this->gameRepository->findOneBy('id', $id);

        if(!$game)
        {
            return;
        }

        $result = $this->gameResource->setDeleted($id, 1);

        if (!$result) {
            return;
        }

        $url = '/game/list/';
        $this->redirectToRoute($url);
    }

    public function restoreAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $game = $this->gameRepository->findOneBy('id', $id, true);

        if(!$game)
        {
            return;
        }

        $result = $this->gameResource->setDeleted($id, 0);

        if (!$result) {
            return;
        }

        $url = '/game/list/';
        $this->redirectToRoute($url);
    }

    public function detailAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $game = $this->gameRepository->findOneBy('id', $id);

        if(!$game)
        {
            return;
        }

        $game->setGenres($this->gameRepository->findGenreNames($game->getID()));

        $this->view->render('game/detail', [
            'game' => $game
        ]);
    }

    public function listAction()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $games = $this->gameRepository->getList(true);
        foreach($games as $game)
        {
            $game->setGenres($this->gameRepository->findGenreNames($game->getID()));
        }

        $this->view->render('game/list', [
            'games' => $games
        ]);
    }

    public function compareAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $game = $this->gameRepository->findOneBy('id', $id);

        if(!$game)
        {
            return;
        }

        $user = $this->session->getCurrentUser();

        $compareError = null;

        if(!$user || !$user->cpufreq || !$user->cpucores || !$user->gpuvram || !$user->ram || !$user->storagespace)
        {
            $compareError = 'Your PC specs are not complete';
        }

        $this->view->render('game/compare', [
            'game' => $game,
            'user' => $user,
            'error' => $compareError
        ]);
    }

    public function bestDealAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $game = $this->gameRepository->findOneBy('id', $id);

        if(!$game)
        {
            return;
        }

        $gameLookup = new Curl\CurlGet('games', [
            'title' => $game->name
        ]);

        $deals = $gameLookup->getResponse();

        if(!$deals)
        {
            return;
        }

        $this->view->render('game/deals', [
            'deals' => json_decode($deals),
            'game' => $game
        ]);
    }
}