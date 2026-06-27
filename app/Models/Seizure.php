<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seizure extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_time',
        'duration_seconds',
        'type',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function triggers()
    {
        return $this->belongsToMany(Trigger::class, 'seizure_trigger');
    }
}
