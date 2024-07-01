<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use test\Mockery\HasUnknownClassAsTypeHintOnMethod;

class Transaction extends Model
{
    use SoftDeletes;

    protected $table = 'transactions';
    protected $fillable = ['user_id', 'balance', 'in', 'out', 'note'];
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];

    protected $casts =[
'created_at' => 'datetime:Y-m-d H:i',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
