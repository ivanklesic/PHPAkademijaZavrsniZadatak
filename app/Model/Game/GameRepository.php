<?php


namespace App\Model\Game;


use App\Core\Data\Database;
use App\Core\Model\RepositoryInterface;

class GameRepository implements RepositoryInterface
{
    public function getList()
    {
        $list = [];
        $db = Database::getInstance();
        $statement = $db->prepare("select * from game where not deleted");
        $statement->execute();
        foreach ($statement->fetchAll() as $game) {
            $list[] = new Game([
                'id' => $game->id,
                'name' => $game->name,
                'releasedate' => $game->releasedate,
                'totalratingssum' => $game->totalratingssum,
                'totalratingscount' => $game->totalratingscount,
                'imageurl' => $game->imageurl,
                'cpufreq' => $game->cpufreq,
                'cpucores' => $game->cpucores,
                'gpuvram' => $game->gpuvram,
                'ram' => $game->ram,
                'storage' => $game->storage
            ]);
        }
        return $list;
    }

    public function propertyExists($key, $value)
    {
        $db = Database::getInstance();
        $statement = $db->prepare('select id from game where not deleted and '.$key.' = (?)', [$value]);
        $statement->execute([
            $value
        ]);
        $fetched = $statement->rowCount();
        return (bool)$fetched;
    }

    public function findOneBy($key, $value)
    {
        $game = false;
        $db = Database::getInstance();
        $statement = $db->prepare('select * from game where not deleted and '.$key.' = (?) ', [$value]);
        $statement->execute([
            $value
        ]);
        foreach ($statement->fetchAll() as $game) {
            $game = new Game([
                'id' => $game->id,
                'name' => $game->name,
                'releasedate' => $game->releasedate,
                'totalratingssum' => $game->totalratingssum,
                'totalratingscount' => $game->totalratingscount,
                'imageurl' => $game->imageurl,
                'cpufreq' => $game->cpufreq,
                'cpucores' => $game->cpucores,
                'gpuvram' => $game->gpuvram,
                'ram' => $game->ram,
                'storage' => $game->storage
            ]);
        }
        return $game;
    }

}