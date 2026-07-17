<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'artisan_id',
        'canal_contact',
        'statut_client',
        'statut_artisan',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function artisan()
    {
        return $this->belongsTo(Artisan::class);
    }

    public function avis()
    {
        return $this->hasMany(Avis::class);
    }
}
