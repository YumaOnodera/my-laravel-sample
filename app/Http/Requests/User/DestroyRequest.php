<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DestroyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user->is_admin || $user->id === (int) $this->id;
    }

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
                    ->withoutTrashed(),
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
