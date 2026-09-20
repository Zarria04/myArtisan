<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Artisan;
use App\Models\Client;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $artisansData = [
            ['nom' => 'Alaoui', 'prenom' => 'Hassan', 'ville' => 'Casablanca', 'secteur' => 'Plomberie', 'cin' => 'BE111222'],
            ['nom' => 'Idrissi', 'prenom' => 'Mehdi', 'ville' => 'Rabat', 'secteur' => 'Électricité', 'cin' => 'BE222333'],
            ['nom' => 'Tazi', 'prenom' => 'Nabil', 'ville' => 'Marrakech', 'secteur' => 'Peinture', 'cin' => 'BE333444'],
            ['nom' => 'Chraibi', 'prenom' => 'Omar', 'ville' => 'Casablanca', 'secteur' => 'Menuiserie', 'cin' => 'BE444555'],
            ['nom' => 'Benjelloun', 'prenom' => 'Rachid', 'ville' => 'Fès', 'secteur' => 'Climatisation', 'cin' => 'BE555666'],
            ['nom' => 'Fassi', 'prenom' => 'Amine', 'ville' => 'Marrakech', 'secteur' => 'Plomberie', 'cin' => 'BE666777'],
            ['nom' => 'Berrada', 'prenom' => 'Younes', 'ville' => 'Rabat', 'secteur' => 'Maçonnerie', 'cin' => 'BE777888'],
            ['nom' => 'Squalli', 'prenom' => 'Ayoub', 'ville' => 'Casablanca', 'secteur' => 'Électricité', 'cin' => 'BE888999'],
        ];

        foreach ($artisansData as $i => $a) {
            $user = User::create([
                'nom' => $a['nom'],
                'prenom' => $a['prenom'],
                'email' => strtolower($a['prenom'] . '.' . $a['nom']) . '@demo.com',
                'password' => Hash::make('password'),
                'telephone' => '06' . str_pad((string)(10000000 + $i), 8, '0', STR_PAD_LEFT),
                'role' => 'artisan',
            ]);

            Artisan::create([
                'user_id' => $user->id,
                'cin' => $a['cin'],
                'ville' => $a['ville'],
                'secteur_activite' => $a['secteur'],
                'compteur_missions_declarees' => rand(0, 6),
                'compteur_missions_confirmees' => rand(0, 4),
                'est_verifie' => 0,
                'badge_orange' => 0,
            ]);
        }

        $clientsData = [
            ['nom' => 'Benali', 'prenom' => 'Salma', 'ville' => 'Casablanca'],
            ['nom' => 'Lahlou', 'prenom' => 'Imane', 'ville' => 'Rabat'],
            ['nom' => 'Kadiri', 'prenom' => 'Yassine', 'ville' => 'Marrakech'],
            ['nom' => 'Ziani', 'prenom' => 'Sofia', 'ville' => 'Fès'],
        ];

        foreach ($clientsData as $i => $c) {
            $user = User::create([
                'nom' => $c['nom'],
                'prenom' => $c['prenom'],
                'email' => strtolower($c['prenom'] . '.' . $c['nom']) . '@demo.com',
                'password' => Hash::make('password'),
                'telephone' => '06' . str_pad((string)(20000000 + $i), 8, '0', STR_PAD_LEFT),
                'role' => 'client',
            ]);

            Client::create([
                'user_id' => $user->id,
                'ville' => $c['ville'],
            ]);
        }
    }
}