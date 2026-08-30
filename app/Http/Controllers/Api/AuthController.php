<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Artisan;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Inscription
    public function register(Request $request)
    {
        // 1. Valider les données reçues
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|in:client,artisan',
            'ville' => 'required|string|max:255',
            // Champs spécifiques artisan (obligatoires seulement si role = artisan)
            'cin' => 'required_if:role,artisan|string|max:50',
            'secteur_activite' => 'required_if:role,artisan|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Créer le User (commun à tous)
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone,
            'role' => $request->role,
        ]);

        // 3. Créer la partie spécifique (Artisan ou Client)
        if ($request->role === 'artisan') {
            Artisan::create([
                'user_id' => $user->id,
                'cin' => $request->cin,
                'ville' => $request->ville,
                'secteur_activite' => $request->secteur_activite,
            ]);
        } else {
            Client::create([
                'user_id' => $user->id,
                'ville' => $request->ville,
            ]);
        }

        // 4. Générer un token pour connecter directement l'utilisateur après inscription
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inscription réussie',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // Connexion
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }

        // Charger le profil Artisan ou Client selon le rôle
        if ($user->role === 'artisan') {
            $user->load('artisan');
        } elseif ($user->role === 'client') {
            $user->load('client');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    // Récupérer l'utilisateur connecté (via token)
    public function me(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'artisan') {
            $user->load('artisan');
        } elseif ($user->role === 'client') {
            $user->load('client');
        }

        return response()->json([
            'user' => $user,
        ], 200);
    }
}
