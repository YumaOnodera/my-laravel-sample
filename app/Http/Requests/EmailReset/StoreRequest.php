<?php

namespace App\Http\Requests\EmailReset;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'new_email' => 'required|string|email|max:255|unique:users,email|unique:email_resets',
        ];
    }
}
