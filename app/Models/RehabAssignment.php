<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RehabAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'specialist_id',
        'activity_id',
        'status',
        'notes',
        'difficulty',
        'duration_minutes',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }

    public function activity()
    {
        return $this->belongsTo(RehabilitationActivity::class, 'activity_id');
    }

    public function result()
    {
        return $this->hasOne(RehabResult::class, 'assignment_id');
    }
}
