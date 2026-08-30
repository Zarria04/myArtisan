<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\Client;
use App\Models\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DemandeController extends Controller
{
    // Créer une demande (le client contacte un artisan)
    public function store(Request $request)
{
    $user = $request->user();

    if ($user->role !== 'client') {
        return response()->json(['message' => 'Seul un client peut créer une demande'], 403);
    }

    $validator = Validator::make($request->all(), [
        'artisan_id' => 'required|exists:artisans,id',
        'contact_canal' => 'required|in:whatsapp,email,appel',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $client = Client::where('user_id', $user->id)->first();

    // Vérifier si une demande NON TERMINÉE existe déjà entre ce client et cet artisan
    $demandeEnCours = Demande::where('client_id', $client->id)
        ->where('artisan_id', $request->artisan_id)
        ->where(function ($query) {
            $query->where('statut_client', 'en_attente')
                  ->orWhere('statut_artisan', 'en_attente');
        })
        ->first();

    if ($demandeEnCours) {
        return response()->json([
            'message' => 'Une demande est déjà en cours avec cet artisan',
            'demande' => $demandeEnCours,
        ], 200);
    }

    $statutDefaut = 'en_attente';

    $demande = Demande::create([
        'client_id' => $client->id,
        'artisan_id' => $request->artisan_id,
        'contact_canal' => $request->contact_canal,
        'statut_client' => $statutDefaut,
        'statut_artisan' => $statutDefaut,
    ]);

    return response()->json([
        'message' => 'Demande créée avec succès',
        'demande' => $demande,
    ], 201);
}

   public function index(Request $request)
{
    $user = $request->user();

    if ($user->role === 'client') {
        $client = Client::where('user_id', $user->id)->first();

        if (!$client) {
            return response()->json(['message' => 'Client introuvable'], 404);
        }

        $demandes = Demande::with('artisan.user')
            ->where('client_id', $client->id)
            ->get();

    } elseif ($user->role === 'artisan') {
        $artisan = Artisan::where('user_id', $user->id)->first();

        if (!$artisan) {
            return response()->json(['message' => 'Artisan introuvable'], 404);
        }

        $demandes = Demande::with('client.user')
            ->where('artisan_id', $artisan->id)
            ->get();

    } else {
        return response()->json(['message' => 'Rôle non autorisé'], 403);
    }

    return response()->json([
        'count' => $demandes->count(),
        'demandes' => $demandes,
    ], 200);
}


public function confirmer(Request $request, $id)
{
    $user = $request->user();
    $demande = Demande::find($id);

    if (!$demande) {
        return response()->json(['message' => 'Demande introuvable'], 404);
    }

    // On mémorise l'état AVANT modification, pour éviter le double-comptage
    $etaitDejaConfirmee = ($demande->statut_client === 'oui' && $demande->statut_artisan === 'oui');

    if ($user->role === 'artisan') {
        $artisan = Artisan::where('user_id', $user->id)->first();

        if (!$artisan || $demande->artisan_id !== $artisan->id) {
            return response()->json(['message' => 'Non autorisé pour cette demande'], 403);
        }

        $dejaDeclareAvant = $demande->statut_artisan === 'oui';
        $demande->statut_artisan = 'oui';
        $demande->save();

        if (!$dejaDeclareAvant) {
            $artisan->increment('compteur_missions_declarees');
        }

    } elseif ($user->role === 'client') {
        $client = Client::where('user_id', $user->id)->first();

        if (!$client || $demande->client_id !== $client->id) {
            return response()->json(['message' => 'Non autorisé pour cette demande'], 403);
        }

        $demande->statut_client = 'oui';
        $demande->save();

    } else {
        return response()->json(['message' => 'Rôle non autorisé'], 403);
    }

    // On incrémente "confirmée" seulement si ça VIENT de devenir les deux "oui"
    $estMaintenantConfirmee = ($demande->statut_client === 'oui' && $demande->statut_artisan === 'oui');

    if ($estMaintenantConfirmee && !$etaitDejaConfirmee) {
        $artisan = Artisan::find($demande->artisan_id);
        $artisan->increment('compteur_missions_confirmees');
    }

    return response()->json([
        'message' => 'Confirmation enregistrée',
        'demande' => $demande,
    ], 200);
}
}



/*public function confirmer(Request $request, $id)
{
    $user = $request->user();
    $demande = Demande::find($id);

    if (!$demande) {
        return response()->json(['message' => 'Demande introuvable'], 404);
    }

    if ($user->role === 'artisan') {
        $artisan = Artisan::where('user_id', $user->id)->first();

        if (!$artisan || $demande->artisan_id !== $artisan->id) {
            return response()->json(['message' => 'Non autorisé pour cette demande'], 403);
        }

        $dejaConfirmeAvant = $demande->statut_artisan === 'oui';
        $demande->statut_artisan = 'oui';
        $demande->save();

        // Compteur "déclarée" incrémenté une seule fois
        if (!$dejaConfirmeAvant) {
            $artisan->increment('compteur_missions_declarees');
        }

    } elseif ($user->role === 'client') {
        $client = Client::where('user_id', $user->id)->first();

        if (!$client || $demande->client_id !== $client->id) {
            return response()->json(['message' => 'Non autorisé pour cette demande'], 403);
        }

        $demande->statut_client = 'oui';
        $demande->save();

    } else {
        return response()->json(['message' => 'Rôle non autorisé'], 403);
    }

    // Si les deux ont confirmé -> compteur "confirmée" (une seule fois)
    if ($demande->statut_client === 'oui' && $demande->statut_artisan === 'oui') {
        $artisan = Artisan::find($demande->artisan_id);
        
        // On vérifie qu'on ne l'a pas déjà comptée en confirmée avant
        // (simple : on incrémente seulement au moment où ça devient "les deux oui")
        $artisan->increment('compteur_missions_confirmees');
    }

    return response()->json([
        'message' => 'Confirmation enregistrée',
        'demande' => $demande,
    ], 200);
}*/