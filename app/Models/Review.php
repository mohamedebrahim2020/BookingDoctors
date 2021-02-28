<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = [
        'rank', 'comment', 'respond'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
