<?php


namespace App\Core\Data;
use Exception;


abstract class DataObject implements DataObjectInterface
{
    protected $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function __get($key)
    {
        return $this->data[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    public function __unset($key)
    {
        unset($this->data[$key]);
    }

    public function __call($name, $arguments){
        $methodPrefix = substr($name, 0, 3);
        $methodMainName = strtolower(substr($name, 3));
        $argument = $arguments[0] ?? null;

        switch ($methodPrefix)
        {
            case 'get':
                return $this->__get($methodMainName) ?? null;
            case 'set':
                $this->__set($methodMainName, $argument);
                return $this;
            case 'has':
                return $this->__isset($methodMainName);
            case 'uns':
                $this->__unset($methodMainName);
                return $this;
            default:
                throw new Exception('Wrong method prefix.');

        }
    }

}