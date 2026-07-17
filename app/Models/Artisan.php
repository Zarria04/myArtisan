<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artisan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cin',
        'photo_profil',
        'ville',
        'secteur_activite',
        'zone_intervention',
        'annees_experience',
        'portfolio',
        'diplome',
        'disponibilite_generale',
        'description',
        'est_verifie',
        'compteur_missions_declarees',
        'compteur_missions_confirmees',
        'badge_orange',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demandes::class);
    }
}
