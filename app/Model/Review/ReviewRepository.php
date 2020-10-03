<?php


namespace App\Model\Review;


use App\Core\Data\Database;
use App\Core\Model\RepositoryInterface;

class ReviewRepository implements RepositoryInterface
{
    public function getList($all = null)
    {
        $list = [];
        $db = Database::getInstance();
        $statement = $db->prepare("select * from review ");
        $statement->execute();
        foreach ($statement->fetchAll() as $review) {
            $list[] = new Review([
                'id' => $review->id,
                'userid' => $review->userid,
                'gameid' => $review->gameid,
                'rating' => $review->rating,
                'title' => $review->title,
                'reviewtext' => $review->reviewtext,
            ]);
        }
        return $list;
    }

    public function propertyExists($key, $value, $all = null)
    {
        $db = Database::getInstance();
        $statement = $db->prepare('select id from review where '.$key.' = (?)', [$value]);
        $statement->execute([
            $value
        ]);
        $fetched = $statement->rowCount();
        return (bool)$fetched;
    }

    public function findOneBy($key, $value, $all = false)
    {
        $review = false;
        $db = Database::getInstance();
        $statement = $db->prepare('select r.id, r.userID, r.gameID, r.rating, r.title, r.reviewtext, g.name as game, CONCAT(u.firstname, " " , u.lastname) as user, u.imageurl from review r 
                                            inner join game g on r.gameID = g.id
                                            inner join user u on r.userID = u.id 
                                            where r.'.$key.' = (?) ', [$value]);
        $statement->execute([
            $value
        ]);
        foreach ($statement->fetchAll() as $review) {
            $review = new Review([
                'id' => $review->id,
                'userID' => $review->userID,
                'gameID' => $review->gameID,
                'rating' => $review->rating,
                'title' => $review->title,
                'reviewtext' => $review->reviewtext,
                'game' => $review->game,
                'user' => $review->user,
                'image' => $review->imageurl
            ]);
        }
        return $review;
    }

    public function findBy($key, $value)
    {
        $list = [];
        $db = Database::getInstance();
        $statement = $db->prepare('select r.id, r.userID, r.gameID, r.rating, r.title, r.reviewtext, g.name as game, CONCAT(u.firstname, " " , u.lastname) as user from review r 
                                            inner join game g on r.gameID = g.id
                                            inner join user u on r.userID = u.id 
                                            where r.'.$key.' = (?) ', [$value]);
        $statement->execute([
            $value
        ]);
        foreach ($statement->fetchAll() as $review) {
            $list[] = new Review([
                'id' => $review->id,
                'userID' => $review->userID,
                'gameID' => $review->gameID,
                'rating' => $review->rating,
                'title' => $review->title,
                'reviewtext' => $review->reviewtext,
                'game' => $review->game,
                'user' => $review->user
            ]);
        }
        return $list;
    }

    public function findOneByGameAndUser($gameID, $userID)
    {
        $review = false;
        $db = Database::getInstance();
        $statement = $db->prepare('select * from review where userID = (?) and gameID = (?)', [$userID, $gameID]);
        $statement->execute([
            $userID,
            $gameID
        ]);
        foreach ($statement->fetchAll() as $review) {
            $review = new Review([
                'id' => $review->id,
                'userID' => $review->userID,
                'gameID' => $review->gameID,
                'rating' => $review->rating,
                'title' => $review->title,
                'reviewtext' => $review->reviewtext
            ]);
        }
        return $review;
    }

}