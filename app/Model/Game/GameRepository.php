<?php


namespace App\Model\Game;


use App\Core\Data\Database;
use App\Core\Model\RepositoryInterface;

class GameRepository implements RepositoryInterface
{
    public function getList($all = false)
    {
        $list = [];
        $db = Database::getInstance();
        $countDeleted = $all ? '' : 'where not deleted';
        $statement = $db->prepare('select * from game ' . $countDeleted);
        $statement->execute();
        foreach ($statement->fetchAll() as $game) {
            $list[] = new Game([
                'id' => $game->id,
                'name' => $game->name,
                'releasedate' => $game->releasedate,
                'totalratingssum' => $game->totalratingssum,
                'totalratingscount' => $game->totalratingscount,
                'cpufreq' => $game->cpufreq,
                'cpucores' => $game->cpucores,
                'gpuvram' => $game->gpuvram,
                'ram' => $game->ram,
                'storagespace' => $game->storagespace
            ]);
        }
        return $list;
    }

    public function propertyExists($key, $value, $all = false)
    {
        $db = Database::getInstance();
        $countDeleted = $all ? '' : 'not deleted and ';
        $statement = $db->prepare('select id from game where '. $countDeleted . $key .' = (?)', [$value]);
        $statement->execute([
            $value
        ]);
        $fetched = $statement->rowCount();
        return (bool)$fetched;
    }

    public function findOneBy($key, $value, $all = false)
    {
        $game = null;
        $db = Database::getInstance();
        $countDeleted = $all ? '' : 'not deleted and ';
        $statement = $db->prepare('select * from game where '. $countDeleted . $key .' = (?) ', [$value]);
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
                'cpufreq' => $game->cpufreq,
                'cpucores' => $game->cpucores,
                'gpuvram' => $game->gpuvram,
                'ram' => $game->ram,
                'storagespace' => $game->storagespace
            ]);
        }
        return $game;
    }

}