<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trigger extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function seizures()
    {
        return $this->belongsToMany(Seizure::class, 'seizure_trigger');
    }
}
