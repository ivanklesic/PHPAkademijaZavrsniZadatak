<?php


namespace App\Core\Data;


interface DataObjectInterface
{
    public function __construct(array $data);
    public function __call($name, $args);
    public function __get($key);
    public function __set($key, $value);
    public function __isset($key);
    public function __unset($key);
}