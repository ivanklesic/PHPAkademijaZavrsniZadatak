<?php


namespace App\Model\Game;


use App\Core\Data\Database;
use App\Core\Model\ResourceInterface;

class GameResource implements ResourceInterface
{
    public function insert($data)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'INSERT into game (name, releasedate, imageurl, cpufreq, cpucores, gpuvram, ram, storage) 
                      values (:name, :releasedate, :imageurl, :cpufreq, :cpucores, :gpuvram, :ram, :storage)'
        );
        $statement->bindValue('name', $data['name']);
        $statement->bindValue('releasedate', $data['releasedate']);
        $statement->bindValue('imageurl', $data['imageurl'] ?? null);
        $statement->bindValue('cpufreq', $data['cpufreq']);
        $statement->bindValue('cpucores', $data['cpucores']);
        $statement->bindValue('gpuvram', $data['gpuvram']);
        $statement->bindValue('ram', $data['ram']);
        $statement->bindValue('storage', $data['storage']);

        return $statement->execute();
    }

}