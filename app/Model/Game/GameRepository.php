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
        $countDeleted = $all ? '' : 'where deleted = 0';
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
                'storagespace' => $game->storagespace,
                'deleted' => $game->deleted
            ]);
        }
        return $list;
    }

    public function propertyExists($key, $value, $all = false)
    {
        $db = Database::getInstance();
        $countDeleted = $all ? '' : 'deleted = 0 and ';
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
        $countDeleted = $all ? '' : 'deleted = 0 and ';
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
                'storagespace' => $game->storagespace,
                'deleted' => $game->deleted
            ]);
        }
        return $game;
    }

    public function findGenreIDs($gameID)
    {
        $list = [];
        $db = Database::getInstance();
        $statement = $db->prepare(
            'SELECT genreID from game_genre where gameID = (:gameID)'
        );
        $statement->bindValue('gameID', $gameID);
        $statement->execute();
        foreach ($statement->fetchAll() as $id) {
            $list[] = (int)$id->genreID;
        }
        return $list;
    }

    public function findGenreNames($gameID)
    {
        $list = [];
        $db = Database::getInstance();
        $statement = $db->prepare(
            'SELECT g.name from genre g inner join game_genre gg on g.id = gg.genreID
                        where gg.gameID = (:gameID) '
        );
        $statement->bindValue('gameID', $gameID);
        $statement->execute();
        foreach ($statement->fetchAll() as $name) {
            $list[] = $name;
        }
        return $list;
    }

    public function findByGenre($genreID)
    {
        $list = [];
        $db = Database::getInstance();
        $statement = $db->prepare(
            'SELECT game.id, game.name, game.releasedate, game.totalratingssum, game.totalratingscount from game
                        inner join game_genre gg on game.id = gg.gameID
                        inner join genre on genre.id = gg.genreID
                        where genre.id = (:genreID) and game.deleted = 0'
        );
        $statement->bindValue('genreID', $genreID);
        $statement->execute();
        foreach ($statement->fetchAll() as $game) {
            $list[] = new Game([
                'id' => $game->id,
                'name' => $game->name,
                'releasedate' => $game->releasedate,
                'totalratingssum' => $game->totalratingssum,
                'totalratingscount' => $game->totalratingscount
            ]);
        }
        return $list;

    }


}