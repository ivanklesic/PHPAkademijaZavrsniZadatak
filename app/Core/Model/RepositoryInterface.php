<?php


namespace App\Core\Model;


interface RepositoryInterface
{
    public function getList();
    public function propertyExists($key, $value);
    public function findOneBy($key, $value);
}