<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    const IS_ADMIN = 1;
    const IS_NORMAL = 0;
    const IS_BANNED = 1;
    const IS_ACTIVE = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public static function add($fields)
    {
        $user = new static;
        $user->fill($fields);
        $user->save();

        return $user;
    }

    public function edit($fields)
    {
        $this->fill($fields);

        $this->save();
    }

    public function generatePassword($password)
    {
        if ($password != null) {
            $this->password = Hash::make($password);
            $this->save();
        }
    }

    public function remove()
    {
        $this->removeAvatar();
        $this->delete();
    }

    public function uploadAvatar($image)
    {
        if ($image == null) {
            return;
        }
        $this->removeAvatar();

        $filename = Str::random(10) . '.' . $image->extension();
        $image->storeAs('uploads', $filename);
        $this->avatar = $filename;
        $this->save();
    }

    public function removeAvatar()
    {
        if ($this->avatar != null) {
            Storage::delete('uploads/' . $this->avatar);
        }
    }

    public function getAvatar()
    {
        if ($this->avatar == null) {
            return '/img/no-image.png';
        }

        return '/uploads/' . $this->avatar;
    }

    public function makeAdmin()
    {
        $this->is_admin = self::IS_ADMIN;
        $this->save();
    }

    public function makeNormal()
    {
        $this->is_admin = self::IS_NORMAL;
        $this->save();
    }

    public function toggleAdmin($value)
    {
        return $value == null ? $this->makeNormal() : $this->makeAdmin();
    }

    public function ban()
    {
        $this->status = self::IS_BANNED;
        $this->save();
    }

    public function unban()
    {
        $this->is_admin = self::IS_ACTIVE;
        $this->save();
    }

    public function toggleBan($value)
    {
        return $value == null ? $this->unban() : $this->ban();
    }
}
