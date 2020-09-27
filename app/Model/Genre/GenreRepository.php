<?php


namespace App\Model\Genre;


use App\Core\Data\Database;
use App\Core\Model\RepositoryInterface;

class GenreRepository implements RepositoryInterface
{
    public function getList($all = false)
    {
        $list = [];
        $db = Database::getInstance();
        $countDeleted = $all ? '' : 'where not deleted';
        $statement = $db->prepare('select * from genre ' . $countDeleted);
        $statement->execute();
        foreach ($statement->fetchAll() as $genre) {
            $list[] = new Genre([
                'id' => $genre->id,
                'name' => $genre->name,
            ]);
        }
        return $list;
    }

    public function propertyExists($key, $value, $all = false)
    {
        $db = Database::getInstance();
        $countDeleted = $all ? '' : 'not deleted and ';
        $statement = $db->prepare('select id from genre where '. $countDeleted . $key .' = (?)', [$value]);
        $statement->execute([
            $value
        ]);
        $fetched = $statement->rowCount();
        return (bool)$fetched;
    }

    public function findOneBy($key, $value, $all = false)
    {
        $genre = false;
        $db = Database::getInstance();
        $countDeleted = $all ? '' : 'not deleted and ';
        $statement = $db->prepare('select * from genre where '. $countDeleted . $key .' = (?) ', [$value]);
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