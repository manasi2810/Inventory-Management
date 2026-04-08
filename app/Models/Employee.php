<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact_no',
        'address',
        'department',
        'designation',
        'date_of_join',
        'salary',
        'profile_photo',
        'resume',
        'certificates',
        'id_proof',
    ];

    protected $casts = [
        'certificates' => 'array',
        'date_of_join' => 'date',
        'salary' => 'decimal:2',
    ];

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // File URL helpers
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo ? asset('storage/' . $this->profile_photo) : asset('default-profile.png');
    }

    public function getResumeUrlAttribute()
    {
        return $this->resume ? asset('storage/' . $this->resume) : null;
    }

    public function getIdProofUrlAttribute()
    {
        return $this->id_proof ? asset('storage/' . $this->id_proof) : null;
    }
}