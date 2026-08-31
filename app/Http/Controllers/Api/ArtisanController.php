<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artisan;
use App\Models\Avis;
use Illuminate\Http\Request;

class ArtisanController extends Controller
{
    // Recherche d'artisans
    public function search(Request $request)
    {
        $query = Artisan::with('user'); // pour avoir nom, prenom, telephone

        if ($request->filled('ville')) {
            $query->where('ville', 'like', '%' . $request->ville . '%');
        }

        if ($request->filled('secteur_activite')) {
            $query->where('secteur_activite', 'like', '%' . $request->secteur_activite . '%');
        }

        $artisans = $query->get();

        return response()->json([
            'count' => $artisans->count(),
            'artisans' => $artisans,
        ], 200);
    }

    public function show($id)
{
    $artisan = Artisan::with('user')->find($id);

    if (!$artisan) {
        return response()->json(['message' => 'Artisan introuvable'], 404);
    }

    // Récupérer les avis reçus par cet artisan (auteur='client', via les demandes liées)
        $avis = Avis::with('demande.client.user')
    ->whereHas('demande', function ($query) use ($artisan) {
        $query->where('artisan_id', $artisan->id);
    })
    ->where('auteur', 'client')
    ->get();

    $noteMoyenne = $avis->avg('note');

    return response()->json([
        'artisan' => $artisan,
        'avis' => $avis,
        'note_moyenne' => $noteMoyenne ? round($noteMoyenne, 1) : null,
        'nombre_avis' => $avis->count(),
    ], 200);
}
}