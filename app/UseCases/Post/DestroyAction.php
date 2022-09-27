<?php

namespace App\UseCases\Post;

use App\Http\Requests\Post\DestroyRequest;
use App\Models\Post;

class DestroyAction
{
    /**
     * @param  DestroyRequest  $request
     * @param  int  $id
     * @return void
     */
    public function __invoke(DestroyRequest $request, int $id): void
    {
        Post::findOrFail($id)->delete();
    }
}
