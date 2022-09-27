<?php

namespace App\UseCases\Post;

use App\Http\Requests\Post\StoreRequest;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class StoreAction
{
    /**
     * @param  StoreRequest  $request
     * @return Model
     */
    public function __invoke(StoreRequest $request): Model
    {
        return Post::create([
            'user_id' => $request->user()->id,
            'text' => $request->text,
        ]);
    }
}
