<?php

namespace App\Rules;

use App;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rule as ValidationRule;

class RequiredForAdmin implements Rule
{
    private string $message = '';

    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!user()->isSuperAdmin()){
            return true;
        }

        $validator = \Validator::make([$attribute => $value], [
            $attribute => 'required',
        ]);

        if ($validator->fails()){
            $this->message = $validator->messages()->first();
        }

        return $validator->passes();

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
