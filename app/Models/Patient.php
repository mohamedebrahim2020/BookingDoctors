<?php

namespace App\Models;

use App\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class Patient extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'photo', 'gender', 'verified_at'
    ];

    public function verificationCode()
    {
        return $this->hasOne(PatientVerificationCode::class,'patient_id');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get all of the patient`s firebase tokens.
    */
    public function firebaseTokens()
    {
        return $this->morphMany(FirebaseToken::class, 'user');
    }
}
