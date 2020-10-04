<?php


namespace App\Controller;

use App\Core\Controller\AbstractController;
use App\Model\Review;
use App\Model\Game;
use App\Core\Validation\ReviewValidator;

class ReviewController extends AbstractController
{
    private $reviewRepository;
    private $gameRepository;
    private $reviewResource;
    private $reviewValidator;

    public function __construct()
    {
        $this->reviewRepository = new Review\ReviewRepository();
        $this->gameRepository = new Game\GameRepository();
        $this->reviewResource = new Review\ReviewResource();
        $this->reviewValidator = new ReviewValidator();
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

        if($game->deleted)
        {
            return;
        }

        $userID = $this->session->getCurrentUser()->id;

        $review = $this->reviewRepository->findOneByGameAndUser($gameID, $userID);

        if($review) {
            $this->redirectToRoute('/review/edit/' . $review->id);
        }

        $errors = $this->session->errors;
        unset($this->session->errors);

        $this->view->render('review/create', [
            'game' => $game,
            'edit' => false,
            'errors' => $errors
        ]);
    }

    public function createPostAction($gameID)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $postData = $this->request->getBody();

        $userID = $this->session->getCurrentUser()->id;

        if (!$userID || !$gameID) {
            return;
        }

        $postData['userid'] = $userID;
        $postData['gameid'] = $gameID;

        $errors = $this->reviewValidator->validateForm($postData);

        if(!empty($errors))
        {
            $this->session->setErrors($errors);
            $this->redirectToRoute('/review/create/' . $gameID);
            exit();
        }

        $result = $this->reviewResource->insert($postData);

        if (!$result) {
            return;
        }

        $url = '/review/list/' . $gameID;
        $this->redirectToRoute($url);
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

        $errors = $this->session->errors;
        unset($this->session->errors);

        $this->view->render('review/create', [
            'review' => $review,
            'edit' => true,
            'errors' => $errors
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

        if($this->session->getCurrentUser()->getId() !== $review->userID)
        {
            return;
        }

        $postData = $this->request->getBody();

        $errors = $this->reviewValidator->validateForm($postData);

        if(!empty($errors))
        {
            $this->session->setErrors($errors);
            $this->redirectToRoute('/review/edit/' . $id);
            exit();
        }

        $result = $this->reviewResource->save($id, $postData);

        if (!$result) {
            return;
        }

        $url = '/review/detail/' . $id;
        $this->redirectToRoute($url);
    }

    public function deleteAction($id)
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

        $result = $this->reviewResource->delete($id);

        if (!$result) {
            return;
        }

        $this->redirectToRoute();
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