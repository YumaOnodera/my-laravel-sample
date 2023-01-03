<?php

namespace App\UseCases\Post;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class ShowAction
{
    /**
     * @param  int  $id
     * @return Model
     */
    public function __invoke(int $id): Model
    {
        return Post::active()->findOrFail($id);
    }
}
