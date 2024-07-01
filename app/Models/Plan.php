<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    protected $fillable = [
        'name',
        'title',
        'description',
        'credit',
        'period',
        'price',
        'status'
    ];

    public const ACTIVE_STATUS = 'active';

    protected $casts = [
        'credit' => 'integer',
        'period' => 'integer',
        'price' => 'float'
    ];

    protected $appends = [
        'purchased'
    ];

    public function getPurchasedAttribute()
    {
        return $this->users()->where('user_id', \request()->get('user_id'))->exists();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'plan_purchases')->withTimestamps();
    }
}
