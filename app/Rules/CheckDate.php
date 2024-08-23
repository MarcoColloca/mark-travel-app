<?php

namespace App\Rules;


use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class CheckDate implements ValidationRule
{

    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }


    
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $success = false;

        if (strtotime($this->startDate) < strtotime($this->endDate)) {
            $success = true;
        }
        
        if($success === false)
        {            
            $fail('La data di fine deve essere successiva alla data di inizio.');
        }
    }
}
