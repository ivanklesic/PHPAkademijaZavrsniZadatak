<?php


namespace App\Model\User;


use App\Core\Data\Database;
use App\Core\Model\ResourceInterface;

class UserResource implements ResourceInterface
{
    public function insert($data)
    {
        $db = Database::getInstance();
        $statement = $db->prepare(
            'INSERT into game (firstname, lastname, email, pass, imageurl, roles, cpufreq, cpucores, gpuvram, ram, storage) values (:firstname, :lastname, :email, :password, :imageurl, :roles, :cpufreq, :cpucores, :gpuvram, :ram, :storage)'
        );
        $statement->bindValue('firstname', $data['firstname']);
        $statement->bindValue('lastname', $data['lastname']);
        $statement->bindValue('email', $data['email']);
        $statement->bindValue('password', password_hash($data['pass'], PASSWORD_DEFAULT));
        $statement->bindValue('imageurl', $data['imageurl'] ?? null);
        $statement->bindValue('roles', $data['roles']);
        $statement->bindValue('cpufreq', $data['cpufreq'] ?? null);
        $statement->bindValue('cpucores', $data['cpucores'] ?? null);
        $statement->bindValue('gpuvram', $data['gpuvram'] ?? null);
        $statement->bindValue('ram', $data['ram'] ?? null);
        $statement->bindValue('storage', $data['storage'] ?? null);

        return $statement->execute();
    }

}