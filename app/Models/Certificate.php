<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'certificates';

    protected $fillable = [
        'user_id',
        'related_type',
        'related_id',
        'file_pdf',
        'image',
        'from',
        'duration_hours',
        'duration_days',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'duration_hours' => 'integer',
        'duration_days' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    protected $appends = [
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function related()
    {
        return $this->morphTo('related');
    }

    public function getTypeAttribute()
    {
        if ($this->related_type == Course::class) {
            return 'course';
        }

        if ($this->related_type == LiveEvent::class) {
            return 'course';
        }

        return null;
    }

    public function getImageAttribute($image)
    {
        if (!empty($image)) {
            return asset('/upload/files/certificates/' . $image);
        }
        return $image;
    }

    public function getFilePdfAttribute($filePdf)
    {
        if (!empty($filePdf)) {
            return asset('/upload/files/certificates/' . $filePdf);
        }
        return $filePdf;
    }
}
