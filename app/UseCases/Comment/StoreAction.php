<?php

namespace App\UseCases\Comment;

use App\Http\Requests\Comment\StoreRequest;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;

class StoreAction
{
    /**
     * @param StoreRequest $request
     * @return Model
     */
    public function __invoke(StoreRequest $request): Model
    {
        return Comment::create([
            'post_id' => $request->post_id,
            'user_id' => $request->user()->id,
            'text' => $request->text,
        ]);
    }
}
