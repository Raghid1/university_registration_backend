<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Important for Sanctum
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

class Professor extends Authenticatable
{
    protected $fillable=['name','email','password'];

    protected $hidden=['password'];

    public function courses(){
        return $this->hasMany(Course::class,'professor_id','id');
    }
}
