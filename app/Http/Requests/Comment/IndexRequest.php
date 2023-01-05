<?php

namespace App\Http\Requests\Comment;

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
            'cursor' => ['nullable', 'string'],
            'post_id' => ['required', 'integer'],
        ];
    }

    /**
     * @return int
     */
    public function perPage(): int
    {
        return config('const.PER_PAGE.CURSOR_PAGINATE');
    }
}
