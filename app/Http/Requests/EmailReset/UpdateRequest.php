<?php

namespace App\Http\Requests\EmailReset;

use App\Models\EmailReset;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'exists:email_resets'],
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
            'token' => $this->token,
        ]);
    }

    /**
     * @param  Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (! $validator->errors()->isEmpty()) {
                return;
            }

            $emailReset = EmailReset::where('token', $this->token)->first();
            if ($this->tokenExpired($emailReset->created_at)) {
                $emailReset->delete();
                $validator->errors()->add('token', __('exception.email_resets.token_expired'));
            }
        });
    }

    /**
     * トークンが有効期限切れかどうかチェック
     *
     * @param  string  $createdAt
     * @return bool
     */
    private function tokenExpired(string $createdAt): bool
    {
        $expiration = config('const.email_resets.expire');

        return Carbon::parse($createdAt)->addMinutes($expiration)->isPast();
    }
}
