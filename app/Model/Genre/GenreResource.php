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

    public function save($id, $data)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'UPDATE genre SET name = (:name) WHERE id = (:id)'
        );
        $statement->bindValue('name', $data['name']);
        $statement->bindValue('id', $id);

        return $statement->execute();
    }

    public function setDeleted($id, $delete = 1)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'UPDATE genre SET deleted = (:delete) WHERE id = (:id)'
        );
        $statement->bindValue('id', $id);
        $statement->bindValue('delete', $delete);

        return $statement->execute();
    }
}