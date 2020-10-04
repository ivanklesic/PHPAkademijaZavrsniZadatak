<?php


namespace App\Model\User;


use App\Core\Data\Database;
use App\Core\Model\ResourceInterface;

class UserResource implements ResourceInterface
{
    public function insert($data, $admin = false)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'INSERT into user (firstname, lastname, email, pass, imageurl, roles, cpufreq, cpucores, gpuvram, ram, storagespace)
                    values (:firstname, :lastname, :email, :password, :imageurl, :roles, :cpufreq, :cpucores, :gpuvram, :ram, :storagespace)'
        );
        $roles = 'ROLE_USER';
        if($admin)
        {
            $roles .= ',ROLE_ADMIN';
        }
        $statement->bindValue('firstname', $data['firstname']);
        $statement->bindValue('lastname', $data['lastname']);
        $statement->bindValue('email', $data['email']);
        $statement->bindValue('password', password_hash($data['pass'], PASSWORD_DEFAULT));
        $statement->bindValue('imageurl', $data['imageurl'] ?? null);
        $statement->bindValue('roles', $roles);
        $statement->bindValue('cpufreq', $data['cpufreq'] ?? null);
        $statement->bindValue('cpucores', $data['cpucores'] ?? null);
        $statement->bindValue('gpuvram', $data['gpuvram'] ?? null);
        $statement->bindValue('ram', $data['ram'] ?? null);
        $statement->bindValue('storagespace', $data['storagespace'] ?? null);

        return $statement->execute();
    }

    public function save($userID, $data)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'UPDATE user SET firstname = (:firstname), lastname = (:lastname), email = (:email), imageurl = (:imageurl), 
                    cpufreq = (:cpufreq), cpucores = (:cpucores), gpuvram = (:gpuvram), ram = (:ram), storagespace = (:storagespace) WHERE id = (:id)'
        );
        $statement->bindValue('firstname', $data['firstname']);
        $statement->bindValue('lastname', $data['lastname']);
        $statement->bindValue('email', $data['email']);
        $statement->bindValue('imageurl', $data['imageurl'] ?? null);
        $statement->bindValue('cpufreq', $data['cpufreq'] ?? null);
        $statement->bindValue('cpucores', $data['cpucores'] ?? null);
        $statement->bindValue('gpuvram', $data['gpuvram'] ?? null);
        $statement->bindValue('ram', $data['ram'] ?? null);
        $statement->bindValue('storagespace', $data['storagespace'] ?? null);
        $statement->bindValue('id', $userID);

        return $statement->execute();
    }

    public function setDeleted($userID, $delete = 1)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'UPDATE user SET deleted = (:delete) WHERE id = (:id)'
        );
        $statement->bindValue('id', $userID);
        $statement->bindValue('delete', $delete);

        return $statement->execute();
    }

    public function insertGenre($genreID, $userID)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'INSERT into user_genre (userID, genreID)
                    values (:userID, :genreID)'
        );
        $statement->bindValue('userID', $userID);
        $statement->bindValue('genreID', $genreID);

        return $statement->execute();
    }

    public function resetGenres($userID)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'DELETE from user_genre where userID = (:userID)'
        );
        $statement->bindValue('userID', $userID);

        return $statement->execute();
    }

    public function resetPassword($userID, $password)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'UPDATE user SET pass = (:password) WHERE id = (:id)'
        );
        $statement->bindValue('id', $userID);
        $statement->bindValue('password', password_hash($password, PASSWORD_DEFAULT));

        return $statement->execute();
    }


}