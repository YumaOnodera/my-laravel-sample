<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')
                    ->whereNotNull('deleted_at'),
            ],
        ];
    }

    /**
     * バリデーションのためにデータを準備
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->id,
        ]);
    }
}
