<?php


namespace App\Model\Genre;


use App\Core\Data\Database;
use App\Core\Model\RepositoryInterface;

class GenreRepository implements RepositoryInterface
{
    public function getList()
    {
        $list = [];
        $db = Database::getInstance();
        $statement = $db->prepare("select * from genre where not deleted");
        $statement->execute();
        foreach ($statement->fetchAll() as $genre) {
            $list[] = new Genre([
                'id' => $genre->id,
                'name' => $genre->name,
            ]);
        }
        return $list;
    }

    public function propertyExists($key, $value)
    {
        $db = Database::getInstance();
        $statement = $db->prepare('select id from genre where not deleted and '.$key.' = (?)', [$value]);
        $statement->execute([
            $value
        ]);
        $fetched = $statement->rowCount();
        return (bool)$fetched;
    }

    public function findOneBy($key, $value)
    {
        $genre = false;
        $db = Database::getInstance();
        $statement = $db->prepare('select * from genre where not deleted and '.$key.' = (?) ', [$value]);
        $statement->execute([
            $value
        ]);
        foreach ($statement->fetchAll() as $genre) {
            $genre = new Genre([
                'id' => $genre->id,
                'name' => $genre->name,
            ]);
        }
        return $genre;
    }

}