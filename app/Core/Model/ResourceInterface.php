<?php


namespace App\Core\Model;


interface ResourceInterface
{
    public function insert($data);
    public function save($id, $data);
}