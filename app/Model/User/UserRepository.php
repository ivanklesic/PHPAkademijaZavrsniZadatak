<?php


namespace App\Model\User;


use App\Core\Data\Database;
use App\Core\Model\RepositoryInterface;

class UserRepository implements RepositoryInterface
{
    public function getList($all = false)
    {
        $list = [];
        $db = Database::getInstance();
        $countDeleted = $all ? '' : 'where deleted = 0';
        $statement = $db->prepare('select * from user ' . $countDeleted);
        $statement->execute();
        foreach ($statement->fetchAll() as $user) {
            $list[] = new User([
                'id' => $user->id,
                'firstname' => $user->firstname,
                'email' => $user->email,
                'password' => $user->pass,
                'lastname' => $user->lastname,
                'roles' => $user->roles,
                'imageurl' => $user->imageurl,
                'cpufreq' => $user->cpufreq,
                'cpucores' => $user->cpucores,
                'gpuvram' => $user->gpuvram,
                'ram' => $user->ram,
                'storagespace' => $user->storagespace,
                'deleted' => $user->deleted
            ]);
        }
        return $list;
    }

    public function propertyExists($key, $value, $all = false)
    {
        $db = Database::getInstance();
        $countDeleted = $all ? '' : 'deleted = 0 and ';
        $statement = $db->prepare('select id from user where '. $countDeleted . $key .' = (?)', [$value]);
        $statement->execute([
            $value
        ]);
        $fetched = $statement->rowCount();
        return (bool)$fetched;
    }

    public function findOneBy($key, $value, $all = false)
    {
        $user = false;
        $db = Database::getInstance();
        $countDeleted = $all ? '' : 'deleted = 0 and ';
        $statement = $db->prepare('select * from user where '. $countDeleted . $key .' = (?) ', [$value]);
        $statement->execute([
            $value
        ]);
        foreach ($statement->fetchAll() as $user) {
            $user = new User([
                'id' => $user->id,
                'email' => $user->email,
                'password' => $user->pass,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'roles' => $user->roles,
                'imageurl' => $user->imageurl,
                'cpufreq' => $user->cpufreq,
                'cpucores' => $user->cpucores,
                'gpuvram' => $user->gpuvram,
                'ram' => $user->ram,
                'storagespace' => $user->storagespace,
                'deleted' => $user->deleted
            ]);
        }
        return $user;
    }

    public function findGenreIDs($userID)
    {
        $list = [];
        $db = Database::getInstance();
        $statement = $db->prepare(
            'SELECT genreID from user_genre where userID = (:userID)'
        );
        $statement->bindValue('userID', $userID);
        $statement->execute();
        foreach ($statement->fetchAll() as $id) {
            $list[] = (int)$id->genreID;
        }
        return $list;
    }

    public function findGenreNames($userID)
    {
        $list = [];
        $db = Database::getInstance();
        $statement = $db->prepare(
            'SELECT g.name from genre g inner join user_genre ug on g.id = ug.genreID
                        where gg.userID = (:userID) '
        );
        $statement->bindValue('userID', $userID);
        $statement->execute();
        foreach ($statement->fetchAll() as $name) {
            $list[] = $name;
        }
        return $list;
    }

}