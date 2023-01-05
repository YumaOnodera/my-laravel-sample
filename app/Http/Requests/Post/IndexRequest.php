<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'keyword' => ['nullable', 'string'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['nullable', 'integer'],
            'order' => ['nullable', 'string', 'in:popular'],
        ];
    }

    /**
     * @return int
     */
    public function perPage(): int
    {
        return $this->input('per_page', config('const.PER_PAGE.PAGINATE'));
    }

    /**
     * @return array|null
     */
    public function order(): array|null
    {
        if (! $this->keyword) {
            return [
                'id' => 'desc',
            ];
        }

        return null;
    }
}
