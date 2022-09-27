<?php

namespace App\UseCases\Comment;

use App\Http\Requests\Comment\DestroyRequest;
use App\Models\Comment;

class DestroyAction
{
    /**
     * @param  DestroyRequest  $request
     * @param  int  $id
     * @return void
     */
    public function __invoke(DestroyRequest $request, int $id): void
    {
        Comment::findOrFail($id)->delete();
    }
}
