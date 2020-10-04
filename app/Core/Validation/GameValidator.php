<?php


namespace App\Core\Validation;

use App\Model\Game;


class GameValidator extends AbstractValidator
{
    private $gameRepository;

    public function __construct()
    {
        $this->gameRepository = new Game\GameRepository();
        parent::__construct();
    }
    public function validateForm($data, $type = null)
    {
        if(!isset($data['name']) || empty($data['name']) || ctype_space($data['name']))
            $this->errors['game-name'][]= 'Name field is empty';
        if(!isset($data['genres']) || empty($data['genres']))
            $this->errors['game-genres'][]= 'Genres field is empty';
        if(!isset($data['releasedate']) || empty($data['releasedate']))
            $this->errors['game-releasedate'][]= 'Release date field is empty';

        foreach($data as $key => $value)
        {
            switch($key)
            {
                case 'name':
                    if(!is_string($value) || strlen($value) > 90)
                        $this->errors['game-'.$key][]= 'Game name must be less than 90 characters long';
                    if($type = 'create' && $this->gameRepository->propertyExists('name', $value))
                        $this->errors['game-'.$key][]= 'Game with this name already exists';
                    break;
            }
        }
        return $this->errors;
    }


}