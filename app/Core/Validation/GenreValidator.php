<?php


namespace App\Core\Validation;

use App\Model\Genre;

class GenreValidator extends AbstractValidator
{
    private $genreRepository;

    public function __construct()
    {
        $this->genreRepository = new Genre\GenreRepository();
        parent::__construct();
    }

    public function validateForm($data, $type = null)
    {
        if(!isset($data['name']) || empty($data['name']) || ctype_space($data['name']))
            $this->errors['genre-name'][]= 'Name field is empty';

        foreach($data as $key => $value)
        {
            switch($key)
            {
                case 'name':
                    if(!is_string($value) || strlen($value) > 90)
                        $this->errors['genre-'.$key][]= 'Genre name must be less than 90 characters long';
                    if($type = 'create' && $this->genreRepository->propertyExists('name', $value))
                        $this->errors['genre-'.$key][]= 'Genre with this name already exists';
                    break;
            }
        }
        return $this->errors;
    }
}