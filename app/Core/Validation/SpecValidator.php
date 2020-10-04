<?php


namespace App\Core\Validation;


class SpecValidator extends AbstractValidator
{
    public function validateRegister($data, $type = null)
    {
        if($type == 'game')
        {
            if(!isset($data['cpufreq']) || empty($data['cpufreq']))
            {
                $this->errors['register-cpufreq'][]= 'CPU frequency field is empty';
            }
            if(!isset($data['storagespace']) || empty($data['storagespace']))
            {
                $this->errors['register-storagespace'][]= 'Storage field is empty';
            }
            if(!isset($data['cpucores']) || empty($data['cpucores']))
            {
                $this->errors['register-cpucores'][]= 'CPU cores field is empty';
            }
            if(!isset($data['gpuvram']) || empty($data['gpuvram']))
            {
                $this->errors['register-gpuvram'][]= 'GPU VRAM field is empty';
            }
            if(!isset($data['ram']) || empty($data['ram']))
            {
                $this->errors['register-ram'][]= 'RAM field is empty';
            }
        }

        foreach($data as $key => $value)
        {

            switch($key)
            {
                case 'cpufreq':
                    if($value < 0)
                        $this->errors['register-'.$key][]= 'CPU frequency must be positive';
                    break;
                case 'storagespace':
                    if($value < 0)
                        $this->errors['register-'.$key][]= 'Storage must be positive';
                    break;
                case 'cpucores':
                    if($value < 0)
                        $this->errors['register-'.$key][]= 'Number of CPU cores must be positive';
                    break;
                case 'gpuvram':
                    if($value < 0)
                        $this->errors['register-'.$key][]= 'GPU  VRAM must be positive';
                    break;
                case 'ram':
                    if($value < 0)
                        $this->errors['register-'.$key][]= 'RAM must be positive';
                    break;
            }
        }
        return $this->errors;
    }

}