<?php


namespace App\Controller;

use App\Core\Controller\AbstractController;
use App\Core\Config;
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

        $this->view->render('createReview', [
            'session' => $this->session,
            'game' => $game
        ]);
    }

    public function createPostAction()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $postData = $this->request->getBody();
        $userID = $postData['userID'];
        $gameID = $postData['gameID'];
        $rating = $postData['rating'];
        $title = $postData['title'];
        $reviewtext = $postData['reviewtext'];

        if (!$userID || !$gameID || !$rating || !$title || !$reviewtext) {
            return;
        }

        if($this->reviewRepository->propertyExists('userID', $userID) && $this->reviewRepository->propertyExists('gameID', $gameID)) {
            return;
        }

        $result = $this->reviewResource->insert($postData);

        if (!$result) {
            return;
        }

        $url = Config::get('url_local') . 'review/list/' . $gameID;
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

        if($this->session->getUser()->getId() !== $review->getUserID())
        {
            return;
        }

        $this->view->render('reviewEdit', [
            'review' => $review,
            'session' => $this->session
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

        if($this->session->getUser()->getId() !== $review->getUserID())
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

        $url = Config::get('url_local') . 'review/details/' . $id;
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

        if($this->session->getUser()->getId() !== $review->getUserID())
        {
            return;
        }

        $result = $this->reviewResource->setDeleted($id);

        if (!$result) {
            return;
        }

        $url = Config::get('url_local') . 'home/index/';
        header('Location: ' . $url);
    }

    public function detailsAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $review = $this->reviewRepository->findOneBy('id', $id);

        if(!$review)
        {
            return;
        }

        $this->view->render('reviewDetails', [
            'review' => $review,
            'session' => $this->session
        ]);
    }

    public function listAction($gameID)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $game = $this->gameRepository->findOneBy('id', $gameID);

        if(!$game)
        {
            return;
        }

        $reviews = $this->isGranted('ROLE_ADMIN') ? $this->reviewRepository->getList(true) : $this->reviewRepository->findBy('gameID', $gameID);

        if(!$reviews)
        {
            return;
        }

        $this->view->render('reviewList', [
            'reviews' => $reviews,
            'game' => $game,
            'session' => $this->session
        ]);
    }

}