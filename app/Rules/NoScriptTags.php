<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoScriptTags implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Reject any input containing script tags or potentially dangerous HTML
        return !preg_match('/<script\b[^>]*>(.*?)<\/script>/is', $value) &&
            !preg_match('/<iframe\b[^>]*>(.*?)<\/iframe>/is', $value) &&
            !preg_match('/javascript:/i', $value) &&
            !preg_match('/on\w+\s*=/i', $value); // onclick, onload, etc.
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute contains potentially dangerous content.';
    }
}
