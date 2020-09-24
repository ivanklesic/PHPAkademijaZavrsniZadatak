<?php


namespace App\Model\Review;


use App\Core\Data\Database;
use App\Core\Model\RepositoryInterface;

class ReviewRepository implements RepositoryInterface
{
    public function getList()
    {
        $list = [];
        $db = Database::getInstance();
        $statement = $db->prepare("select * from review where not deleted");
        $statement->execute();
        foreach ($statement->fetchAll() as $review) {
            $list[] = new Review([
                'id' => $review->id,
                'userid' => $review->userid,
                'gameid' => $review->gameid,
                'rating' => $review->rating,
                'title' => $review->title,
                'reviewtext' => $review->reviewtext
            ]);
        }
        return $list;
    }

    public function propertyExists($key, $value)
    {
        $db = Database::getInstance();
        $statement = $db->prepare('select id from review where not deleted and '.$key.' = (?)', [$value]);
        $statement->execute([
            $value
        ]);
        $fetched = $statement->rowCount();
        return (bool)$fetched;
    }

    public function findOneBy($key, $value)
    {
        $review = false;
        $db = Database::getInstance();
        $statement = $db->prepare('select * from review where not deleted and '.$key.' = (?) ', [$value]);
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
                'reviewtext' => $review->reviewtext
            ]);
        }
        return $review;
    }

}