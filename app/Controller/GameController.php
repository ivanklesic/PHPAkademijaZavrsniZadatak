<?php


namespace App\Controller;

use App\Core\Controller\AbstractController;
use App\Model\Game;
use App\Model\Genre;
use App\Core\Curl;

class GameController extends AbstractController
{
    private $gameRepository;
    private $genreRepository;
    private $gameResource;

    public function __construct()
    {
        $this->gameRepository = new Game\GameRepository();
        $this->genreRepository = new Genre\GenreRepository();
        $this->gameResource = new Game\GameResource();
        parent::__construct();
    }

    public function createAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $this->view->render('game/create', [
            'genres' => $this->genreRepository->getList(),
            'edit' => false
        ]);
    }

    public function createPostAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $postData = $this->request->getBody();
        $name = $postData['name'];
        $releaseDate = $postData['releasedate'];
        $cpuFreq = $postData['cpufreq'];
        $genres = $postData['genres'];
        $cpuCores = $postData['cpucores'];
        $gpuVram = $postData['gpuvram'];
        $ram = $postData['ram'];
        $storageSpace = $postData['storagespace'];

        if (!$name || !$releaseDate || !$cpuFreq || !$cpuCores || !$gpuVram || !$ram || !$storageSpace || empty($genres)) {
            return;
        }

        if($this->gameRepository->propertyExists('name', $name)) {
            return;
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
        header('Location: ' . $url);
    }

    public function editAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $game = $this->gameRepository->findOneBy('id', $id);

        if(!$game)
        {
            return;
        }

        $this->view->render('game/create', [
            'game' => $game,
            'genres' => $this->genreRepository->getList(),
            'edit' => true,
            'gameGenres' => $this->gameRepository->findGenreIDs($game->getId())
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
        $name = $postData['name'];
        $releaseDate = $postData['releasedate'];
        $genres = $postData['genres'];
        $cpuFreq = $postData['cpufreq'];
        $cpuCores = $postData['cpucores'];
        $gpuVram = $postData['gpuvram'];
        $ram = $postData['ram'];
        $storageSpace = $postData['storagespace'];

        if (!$name || !$releaseDate || !$cpuFreq || !$cpuCores || !$gpuVram || !$ram || !$storageSpace || empty($genres)) {
            return;
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
        header('Location: ' . $url);
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
        header('Location: ' . $url);
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
        header('Location: ' . $url);
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

        if(!$user || !$user->cpufreq || !$user->cpucores || !$user->gpuvram || !$user->ram || !$user->storagespace)
        {
            return;
        }

        $this->view->render('game/compare', [
            'game' => $game,
            'user' => $user
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