<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paytabs extends Model
{
    protected $table = 'paytabs';

    protected $fillable = [
        'payment_reference',
        'user_id',
        'related_id',
        'transaction_id',
        'create_response',
        'verify_payment_response',
        'paid',
        'related_type'
    ];

    protected $casts = [
        'create_response' => 'json',
        'verify_payment_response' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function related()
    {
        return $this->morphTo('related', 'related_type', 'related_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
