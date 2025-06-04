<?php

namespace App\Rules;

use App\Http\Helpers\ExceptionHelper;
use App\Http\Helpers\Helper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneMaskRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^(998)(90|91|92|93|94|95|96|97|98|99|33|88|77)[0-9]{7}$/', Helper::clearPhone($value))) {
            $fail(__('phone:invalid'));
        }
    }
}
