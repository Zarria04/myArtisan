<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use App\Models\Demande;
use App\Models\Client;
use App\Models\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AvisController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'demande_id' => 'required|exists:demandes,id',
            'note' => 'required|integer|min:1|max:5',
            'categorie' => 'nullable|string|max:255',
            'commentaire' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $demande = Demande::find($request->demande_id);

        if ($user->role === 'client') {
            $client = Client::where('user_id', $user->id)->first();

            if (!$client || $demande->client_id !== $client->id) {
                return response()->json(['message' => 'Non autorisé pour cette demande'], 403);
            }

            if ($demande->statut_client !== 'oui') {
                return response()->json(['message' => 'Vous devez d\'abord confirmer la mission avant de laisser un avis'], 403);
            }

            $auteur = 'client';

        } elseif ($user->role === 'artisan') {
            $artisan = Artisan::where('user_id', $user->id)->first();

            if (!$artisan || $demande->artisan_id !== $artisan->id) {
                return response()->json(['message' => 'Non autorisé pour cette demande'], 403);
            }

            if ($demande->statut_artisan !== 'oui') {
                return response()->json(['message' => 'Vous devez d\'abord confirmer la mission avant de laisser un avis'], 403);
            }

            $auteur = 'artisan';

        } else {
            return response()->json(['message' => 'Rôle non autorisé'], 403);
        }

        // Empêcher un double avis du même auteur sur la même demande
        $dejaNote = Avis::where('demande_id', $demande->id)
            ->where('auteur', $auteur)
            ->exists();

        if ($dejaNote) {
            return response()->json(['message' => 'Vous avez déjà laissé un avis pour cette demande'], 409);
        }

        $avis = Avis::create([
            'demande_id' => $demande->id,
            'auteur' => $auteur,
            'note' => $request->note,
            'categorie' => $request->categorie,
            'commentaire' => $request->commentaire,
        ]);

        return response()->json([
            'message' => 'Avis enregistré avec succès',
            'avis' => $avis,
        ], 201);
    }
}