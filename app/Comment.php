<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    const IS_ALLOWED = 1;
    const IS_DISALLOWED = 0;

    public function author()
    {
        return $this->hasOne(User::class);
    }

    public function post()
    {
        return $this->hasOne(Post::class);
    }

    public function allow()
    {
        $this->status = self::IS_ALLOWED;
        $this->save();
    }

    public function disallow()
    {
        $this->status = self::IS_DISALLOWED;
        $this->save();
    }

    public function toggleStatus()
    {
        return $this->status = self::IS_DISALLOWED ? $this->allow() : $this->disallow();
    }

    public function remove()
    {
        $this->delete();
    }
}
