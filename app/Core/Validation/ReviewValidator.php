<?php


namespace App\Core\Validation;


class ReviewValidator extends AbstractValidator
{
    public function validateForm($data)
    {
        if(!isset($data['title']))
            $this->errors['review-title'][]= 'Title field is empty';
        if(!isset($data['rating']))
            $this->errors['review-rating'][]= 'Rating field is empty';
        if(!isset($data['reviewtext']))
            $this->errors['review-reviewtext'][]= 'Review text field is empty';
        foreach($data as $key => $value)
        {
            switch($key)
            {
                case 'title':
                    if(!is_string($value) || strlen($value) > 90 || empty($data['title']) || ctype_space($data['title']))
                        $this->errors['review-'.$key][]= 'Title must be a non-blank string less than 90 characters long';
                    break;
                case 'rating':
                    if( $value < 1 || $value > 10)
                        $this->errors['review-'.$key][]= 'Rating must be an integer less than 11 and greater than 0';
                    break;
                case 'reviewtext':
                    if(!is_string($value) || strlen($value) > 10000 || empty($data['reviewtext']) || ctype_space($data['reviewtext']))
                        $this->errors['review-'.$key][]= 'Review text must be a non-blank string less than 10000 characters long';
                    break;
            }
        }
        return $this->errors;
    }

}