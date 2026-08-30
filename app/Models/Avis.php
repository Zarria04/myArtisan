<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    use HasFactory;

    protected $table = 'avis';     

    protected $fillable = [
        'demande_id',
        'auteur',
        'note',
        'categorie',
        'commentaire',
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }
}
