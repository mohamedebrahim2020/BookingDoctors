<?php

namespace App\Models;

use App\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Doctor extends Model
{
    use HasFactory, Notifiable, Filterable;

    protected $fillable = ['activated_at'];

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    
}
