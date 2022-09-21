<?php

namespace App\UseCases\Post;

use App\Http\Requests\Post\UpdateRequest;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class UpdateAction
{
    /**
     * @param UpdateRequest $request
     * @param int $id
     * @return Model
     */
    public function __invoke(UpdateRequest $request, int $id): Model
    {
        $post = Post::where('id', $id);

        $post->update([
            'text' => $request->text
        ]);

        return $post->first();
    }
}
