<?php


namespace App\Controller;

use App\Core\Controller\AbstractController;
use App\Model\Review;
use App\Model\Game;

class ReviewController extends AbstractController
{
    private $reviewRepository;
    private $gameRepository;
    private $reviewResource;

    public function __construct()
    {
        $this->reviewRepository = new Review\ReviewRepository();
        $this->gameRepository = new Game\GameRepository();
        $this->reviewResource = new Review\ReviewResource();
        parent::__construct();
    }

    public function createAction($gameID)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $game = $this->gameRepository->findOneBy('id', $gameID);

        if(!$game)
        {
            return;
        }

        $userID = $this->session->getCurrentUser()->id;

        $review = $this->reviewRepository->findOneByGameAndUser($gameID, $userID);

        if($review) {
            header('Location: /review/edit/' . $review->id);
        }

        $this->view->render('review/create', [
            'game' => $game,
            'edit' => false
        ]);
    }

    public function createPostAction($gameID)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $postData = $this->request->getBody();

        $userID = $this->session->getCurrentUser()->id;
        $postData['userid'] = $userID;
        $postData['gameid'] = $gameID;
        $rating = $postData['rating'];
        $title = $postData['title'];
        $reviewtext = $postData['reviewtext'];

        if (!$userID || !$gameID || !$rating || !$title || !$reviewtext) {
            return;
        }

        if($this->reviewRepository->findOneByGameAndUser($gameID, $userID)) {
            return;
        }

        $result = $this->reviewResource->insert($postData);

        if (!$result) {
            return;
        }

        $url = '/review/list/' . $gameID;
        header('Location: ' . $url);
    }

    public function editAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $review = $this->reviewRepository->findOneBy('id', $id);

        if(!$review)
        {
            return;
        }

        if($this->session->getCurrentUser()->getId() !== $review->userID)
        {
            return;
        }

        $this->view->render('review/create', [
            'review' => $review,
            'edit' => true
        ]);
    }

    public function editPostAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $review = $this->reviewRepository->findOneBy('id', $id);

        if(!$review)
        {
            return;
        }

        if($this->session->getCurrentUser()->getId() !== $review->getUserID())
        {
            return;
        }

        $postData = $this->request->getBody();
        $rating = $postData['rating'];
        $title = $postData['title'];
        $reviewtext = $postData['reviewtext'];

        if (!$rating || !$title || !$reviewtext) {
            return;
        }

        $result = $this->reviewResource->save($id, $postData);

        if (!$result) {
            return;
        }

        $url = '/review/detail/' . $id;
        header('Location: ' . $url);
    }

    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $review = $this->reviewRepository->findOneBy('id', $id);

        if(!$review)
        {
            return;
        }

        if($this->session->getCurrentUser()->getId() !== $review->getUserID())
        {
            return;
        }

        $result = $this->reviewResource->delete($id);

        if (!$result) {
            return;
        }

        header('Location: /');
    }

    public function detailAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $review = $this->reviewRepository->findOneBy('id', $id);

        if(!$review)
        {
            return;
        }

        $this->view->render('review/detail', [
            'review' => $review
        ]);
    }

    public function listAction($gameID = null)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $game = null;

        if($gameID)
        {
            $game = $this->gameRepository->findOneBy('id', $gameID);
        }

        $reviews = $game ? $this->reviewRepository->findBy('gameID', $gameID) : $this->reviewRepository->findBy('userID', $this->session->getCurrentUser()->id);

        $this->view->render('review/list', [
            'reviews' => $reviews,
            'game' => $game
        ]);
    }

}