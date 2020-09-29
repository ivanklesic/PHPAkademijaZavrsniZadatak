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
                'deleted' => $review->deleted
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
        $statement = $db->prepare('select * from review where '.$key.' = (?) ', [$value]);
        $statement->execute([
            $value
        ]);
        foreach ($statement->fetchAll() as $review) {
            $review = new Review([
                'id' => $review->id,
                'userid' => $review->userid,
                'gameid' => $review->gameid,
                'rating' => $review->rating,
                'title' => $review->title,
                'reviewtext' => $review->reviewtext,
                'deleted' => $review->deleted
            ]);
        }
        return $review;
    }

    public function findBy($key, $value, $all = false)
    {
        $list = [];
        $db = Database::getInstance();
        $statement = $db->prepare('select * from review where '.$key.' = (?) ', [$value]);
        $statement->execute([
            $value
        ]);
        foreach ($statement->fetchAll() as $review) {
            $list[] = new Review([
                'id' => $review->id,
                'userid' => $review->userid,
                'gameid' => $review->gameid,
                'rating' => $review->rating,
                'title' => $review->title,
                'reviewtext' => $review->reviewtext,
                'deleted' => $review->deleted
            ]);
        }
        return $list;
    }

}