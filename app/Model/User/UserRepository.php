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
        $countDeleted = $all ? '' : 'where not deleted';
        $statement = $db->prepare('select * from user ' . $countDeleted);
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
                'storagespace' => $user->storagespace
            ]);
        }
        return $list;
    }

    public function propertyExists($key, $value, $all = false)
    {
        $db = Database::getInstance();
        $countDeleted = $all ? '' : 'not deleted and ';
        $statement = $db->prepare('select id from user where '. $countDeleted . $key .' = (?)', [$value]);
        $statement->execute([
            $key
        ]);
        $fetched = $statement->rowCount();
        return (bool)$fetched;
    }

    public function findOneBy($key, $value, $all = false)
    {
        $user = false;
        $db = Database::getInstance();
        $countDeleted = $all ? '' : 'not deleted and ';
        $statement = $db->prepare('select * from user where '. $countDeleted . $key .' = (?) ', [$value]);
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
                'storagespace' => $user->storagespace
            ]);
        }
        return $user;
    }

}