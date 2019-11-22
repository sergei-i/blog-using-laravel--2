<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function author()
    {
        return $this->hasOne(User::class);
    }

    public function post()
    {
        return $this->hasOne(Post::class);
    }
}
