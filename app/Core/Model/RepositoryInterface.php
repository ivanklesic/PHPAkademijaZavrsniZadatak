<?php


namespace App\Core\Model;


interface RepositoryInterface
{
    public function getList($all = false);
    public function propertyExists($key, $value, $all = false);
    public function findOneBy($key, $value, $all = false);
}