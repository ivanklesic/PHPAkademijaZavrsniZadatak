<?php


namespace App\Model\Review;


use App\Core\Data\Database;
use App\Core\Model\ResourceInterface;

class ReviewResource implements ResourceInterface
{
    public function insert($data)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'INSERT into review (userid, gameid, rating, title, reviewtext)
                      values (:userid, :gameid, :rating, :title, :reviewtext)'
        );
        $statement->bindValue('userid', $data['userid']);
        $statement->bindValue('gameid', $data['gameid']);
        $statement->bindValue('rating', $data['rating']);
        $statement->bindValue('title', $data['title']);
        $statement->bindValue('reviewtext', $data['reviewtext']);

        return $statement->execute();
    }

    public function save($id, $data)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'UPDATE review SET rating = (:rating), title = (:title), reviewtext = (:reviewtext) WHERE id = (:id)'
        );
        $statement->bindValue('rating', $data['rating']);
        $statement->bindValue('title', $data['title']);
        $statement->bindValue('reviewtext', $data['reviewtext']);
        $statement->bindValue('id', $id);

        return $statement->execute();
    }

    public function delete($id)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'DELETE FROM review  WHERE id = (:id)'
        );
        $statement->bindValue('id', $id);

        return $statement->execute();
    }
}