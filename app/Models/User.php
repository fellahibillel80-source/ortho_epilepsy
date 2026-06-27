<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'specialty',
        'caregiver_id',
        'clinic_id',
        'emergency_contact',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function seizures()
    {
        return $this->hasMany(Seizure::class);
    }

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function specialists()
    {
        return $this->belongsToMany(User::class, 'patient_specialist', 'patient_id', 'specialist_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function patients()
    {
        return $this->belongsToMany(User::class, 'patient_specialist', 'specialist_id', 'patient_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function testAssignments()
    {
        return $this->hasMany(TestAssignment::class, 'patient_id');
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class, 'patient_id');
    }

    public function rehabAssignments()
    {
        return $this->hasMany(RehabAssignment::class, 'patient_id');
    }

    public function suggestions()
    {
        return $this->hasMany(Suggestion::class, 'specialist_id');
    }
}
