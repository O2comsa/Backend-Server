<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Helpers\Authorization\AuthorizationUserTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Laratrust\Traits\LaratrustUserTrait;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;
class Admin extends Authenticatable implements LaratrustUser
{
    use Notifiable, SoftDeletes;
    use HasRolesAndPermissions; // add this trait to your user model


    protected $table = 'admins';

    protected $fillable = [
        'name', 'email', 'password', 'avatar'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = ['deleted_at'];

    public function gravatar()
    {
        $hash = hash('md5', strtolower(trim($this->attributes['email'])));

        return sprintf("https://www.gravatar.com/avatar/%s?size=150", $hash);
    }

    public function getAvatarAttribute($avatar)
    {
        if (!empty($avatar)) {
            if (Str::contains($avatar, 'https://www.gravatar.com/avatar')) {
                return $avatar;
            }
            if (Str::contains($avatar, 'upload/profile.png')) {
                return request()->getSchemeAndHttpHost() . '/upload/profile.png';
            }
            return request()->getSchemeAndHttpHost() . '/upload/images/admin/' . $avatar;
        }
    }
}
