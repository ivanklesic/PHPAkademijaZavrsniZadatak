<?php


namespace App\Model\Genre;


use App\Core\Data\Database;
use App\Core\Model\ResourceInterface;

class GenreResource implements ResourceInterface
{
    public function insert($data)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'INSERT into genre (name) 
                      values (:name)'
        );

        $statement->bindValue('name', $data['name']);
        return $statement->execute();
    }

}