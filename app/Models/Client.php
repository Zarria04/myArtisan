<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ville',
        'badge_orange',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }
}
