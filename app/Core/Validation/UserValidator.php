<?php


namespace App\Core\Validation;

use App\Model\User;

class UserValidator extends AbstractValidator
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new User\UserRepository();
        parent::__construct();
    }

    public function validateRegister($data, $type = null)
    {
        if(!isset($data['email']))
            $this->errors['register-email'][]= 'Email field is empty';
        if($type == 'register' && !isset($data['pass']))
            $this->errors['register-pass'][]= 'Password field is empty';
        if($type == 'register' && !isset($data['pass-r']))
            $this->errors['register-pass-r'][]= 'Repeat password field is empty';
        if(!isset($data['firstname']) || empty($data['firstname']) || ctype_space($data['firstname']))
            $this->errors['register-firstname'][]= 'First name field is empty';
        if(!isset($data['lastname']) || empty($data['lastname']) || ctype_space($data['lastname']))
            $this->errors['register-lastname'][]= 'Last name field is empty';

        foreach($data as $key => $value)
        {
            switch($key)
            {
                case 'email':
                    if(!filter_var($value, FILTER_VALIDATE_EMAIL))
                        $this->errors['register-'.$key][]= 'Invalid email';
                    if(strlen($value) > 90)
                        $this->errors['register-'.$key][]= 'Email must be less than 90 characters long';
                    if($type == 'register' && $this->userRepository->propertyExists('email', $value))
                        $this->errors['register-'.$key][]= 'User with this email already exists';
                    break;
                case 'pass':
                    if(!is_string($value) || strlen($value) > 30 || strlen($value) < 4)
                        $this->errors['register-'.$key][]= 'Password must be between 4 and 30 characters long';
                    break;
                case 'pass-r':
                    if($value !== $data['pass'] )
                        $this->errors['register-'.$key][]= 'Passwords must match';
                    break;
                case 'lastname':
                case 'firstname':
                    if(!is_string($value) || strlen($value) > 90)
                        $this->errors['register-'.$key][]= 'Name must be less than 90 characters long';
                    break;
            }
        }
        return $this->errors;
    }

    public function validateLogin($data, $user)
    {
        if(!isset($data['email']))
            $this->errors['login-email'][]= 'Email field is empty';
        if(!isset($data['pass']))
            $this->errors['login-pass'][]= 'Password field is empty';

        foreach($data as $key => $value)
        {
            switch($key)
            {
                case 'email':
                    if ($user->deleted)
                        $this->errors['login-'.$key][]= 'This account is deleted';
                    break;
                case 'pass':
                    if (!password_verify($value, $user->getPassword()))
                        $this->errors['login-'.$key][]= 'Wrong password';
                    break;
            }
        }


        return $this->errors;
    }

    public function validateReset($data, $user)
    {
        if(!isset($data['pass-c']))
            $this->errors['reset-pass-c'][]= 'Current password field is empty';
        if(!isset($data['pass']))
            $this->errors['reset-pass'][]= 'Password field is empty';
        if(!isset($data['pass-r']))
            $this->errors['reset-pass-r'][]= 'Repeat password field is empty';


        foreach($data as $key => $value)
        {
            switch($key)
            {
                case 'pass-c':
                    if (!password_verify($value, $user->getPassword()))
                        $this->errors['reset-'.$key][]= 'Wrong password';
                    break;
                case 'pass':
                    if ($data['pass'] !== $data['pass-r'])
                        $this->errors['reset-'.$key][]= 'Passwords must match';
                    break;
            }
        }


        return $this->errors;
    }


}