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
            'INSERT into game (name, releasedate, cpufreq, cpucores, gpuvram, ram, storagespace) 
                      values (:name, :releasedate, :cpufreq, :cpucores, :gpuvram, :ram, :storagespace)'
        );
        $statement->bindValue('name', $data['name']);
        $statement->bindValue('releasedate', $data['releasedate']);
        $statement->bindValue('cpufreq', $data['cpufreq']);
        $statement->bindValue('cpucores', $data['cpucores']);
        $statement->bindValue('gpuvram', $data['gpuvram']);
        $statement->bindValue('ram', $data['ram']);
        $statement->bindValue('storagespace', $data['storagespace']);

        return $statement->execute();
    }

    public function save($id, $data)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'UPDATE game SET name = (:name), releasedate = (:releasedate),  
                    cpufreq = (:cpufreq), cpucores = (:cpucores), gpuvram = (:gpuvram), ram = (:ram), storagespace = (:storagespace) WHERE id = (:id)'
        );
        $statement->bindValue('name', $data['name']);
        $statement->bindValue('releasedate', $data['releasedate']);
        $statement->bindValue('cpufreq', $data['cpufreq']);
        $statement->bindValue('cpucores', $data['cpucores']);
        $statement->bindValue('gpuvram', $data['gpuvram']);
        $statement->bindValue('ram', $data['ram']);
        $statement->bindValue('storagespace', $data['storagespace']);
        $statement->bindValue('id', $id);

        return $statement->execute();
    }

    public function setDeleted($id, $delete = 1)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'UPDATE game SET deleted = (:delete) WHERE id = (:id)'
        );
        $statement->bindValue('id', $id);
        $statement->bindValue('delete', $delete);

        return $statement->execute();
    }

    public function insertGenre($genreID, $gameID)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'INSERT into game_genre (gameID, genreID)
                    values (:gameID, :genreID)'
        );
        $statement->bindValue('gameID', $gameID);
        $statement->bindValue('genreID', $genreID);

        return $statement->execute();
    }

    public function resetGenres($gameID)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'DELETE from game_genre where gameID = (:gameID)'
        );
        $statement->bindValue('gameID', $gameID);

        return $statement->execute();
    }

}