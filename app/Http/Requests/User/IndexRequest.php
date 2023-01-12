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
            'page' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'keyword' => ['nullable', 'string'],
            'active_only' => ['nullable', 'boolean'],
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
            'active_only' => (bool) $this->active_only,
        ]);
    }

    /**
     * @return int
     */
    public function perPage(): int
    {
        return $this->input('per_page', config('const.PER_PAGE.PAGINATE'));
    }
}
