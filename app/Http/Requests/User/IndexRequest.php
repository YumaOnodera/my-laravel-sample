<?php

namespace App\Http\Requests\User;

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
            'keyword' => 'nullable|string',
        ];
    }

    /**
     * @return int
     */
    public function perPage(): int
    {
        return $this->input('per_page', config('const.PER_PAGE.PAGINATE'));
    }
}
