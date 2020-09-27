<?php


namespace App\Controller;

use App\Core\Controller\AbstractController;
use App\Core\Config;
use App\Model\Game;

class GameController extends AbstractController
{
    private $gameRepository;
    private $gameResource;

    public function __construct()
    {
        $this->gameRepository = new Game\GameRepository();
        $this->gameResource = new Game\GameResource();
        parent::__construct();
    }

    public function createAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $this->view->render('createGame', [
            'session' => $this->session
        ]);
    }

    public function createPostAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $postData = $this->request->getBody();
        $name = $postData['name'];
        $releaseDate = $postData['releasedate'];
        $cpuFreq = $postData['cpufreq'];
        $cpuCores = $postData['cpucores'];
        $gpuVram = $postData['gpuvram'];
        $ram = $postData['ram'];
        $storageSpace = $postData['storagespace'];

        if (!$name || !$releaseDate || !$cpuFreq || !$cpuCores || !$gpuVram || !$ram || !$storageSpace) {
            return;
        }

        if($this->gameRepository->propertyExists('name', $name)) {
            return;
        }

        $result = $this->gameResource->insert($postData);

        if (!$result) {
            return;
        }

        $url = Config::get('url_local') . 'game/list/';
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

        $this->view->render('gameEdit', [
            'game' => $game,
            'session' => $this->session
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
        $cpuFreq = $postData['cpufreq'];
        $cpuCores = $postData['cpucores'];
        $gpuVram = $postData['gpuvram'];
        $ram = $postData['ram'];
        $storageSpace = $postData['storagespace'];

        if (!$name || !$releaseDate || !$cpuFreq || !$cpuCores || !$gpuVram || !$ram || !$storageSpace) {
            return;
        }

        $result = $this->gameResource->save($id, $postData);

        if (!$result) {
            return;
        }

        $url = Config::get('url_local') . 'game/list/';
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

        $url = Config::get('url_local') . 'game/list/';
        header('Location: ' . $url);
    }

    public function restoreAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $game = $this->gameRepository->findOneBy('id', $id);

        if(!$game)
        {
            return;
        }

        $result = $this->gameResource->setDeleted($id, 0);

        if (!$result) {
            return;
        }

        $url = Config::get('url_local') . 'game/list/';
        header('Location: ' . $url);
    }

    public function detailsAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $game = $this->gameRepository->findOneBy('id', $id);

        if(!$game)
        {
            return;
        }

        $this->view->render('gameDetails', [
            'game' => $game,
            'session' => $this->session
        ]);
    }

    public function listAction()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $games = $this->isGranted('ROLE_ADMIN') ? $this->gameRepository->getList(true) : $this->gameRepository->getList();

        if(!$games)
        {
            return;
        }

        $this->view->render('gameList', [
            'games' => $games,
            'session' => $this->session
        ]);
    }
}