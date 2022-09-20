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
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
            'order_by' => 'nullable|string|in:created_at',
            'order' => 'nullable|string|in:asc,desc',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'nullable|integer',
        ];
    }

    /**
     * @return int
     */
    public function perPage(): int
    {
        return $this->input('per_page', config('const.PER_PAGE'));
    }
}
