<?php


namespace App\Model\User;


use App\Core\Data\Database;
use App\Core\Model\RepositoryInterface;

class UserRepository implements RepositoryInterface
{
    public function getList()
    {
        $list = [];
        $db = Database::getInstance();
        $statement = $db->prepare("select * from user where not deleted");
        $statement->execute();
        foreach ($statement->fetchAll() as $user) {
            $list[] = new User([
                'id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'roles' => $user->roles,
                'image_url' => $user->imageurl,
                'cpufreq' => $user->cpufreq,
                'cpucores' => $user->cpucores,
                'gpuvram' => $user->gpuvram,
                'ram' => $user->ram,
                'storage' => $user->storage
            ]);
        }
        return $list;
    }

    public function propertyExists($key, $value)
    {
        $db = Database::getInstance();
        $statement = $db->prepare('select id from user where not deleted and '.$key.' = (?)', [$value]);
        $statement->execute([
            $key
        ]);
        $fetched = $statement->rowCount();
        return (bool)$fetched;
    }

    public function findOneBy($key, $value, $findOne = null)
    {
        $user = false;
        $db = Database::getInstance();
        $statement = $db->prepare('select * from user where not deleted and '.$key.' = (?) ', [$value]);
        $statement->execute([
            $value
        ]);
        foreach ($statement->fetchAll() as $user) {
            $user = new User([
                'id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'roles' => $user->roles,
                'imageurl' => $user->imageurl,
                'cpufreq' => $user->cpufreq,
                'cpucores' => $user->cpucores,
                'gpuvram' => $user->gpuvram,
                'ram' => $user->ram,
                'storage' => $user->storage
            ]);
        }
        return $user;
    }

}