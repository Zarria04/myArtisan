<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('artisans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('cin');
            $table->string('photo_profil')->nullable();
            $table->string('ville');
            $table->string('secteur_activite');
            $table->string('zone_intervention')->nullable();
            $table->unsignedTinyInteger('annees_experience')->nullable();
            $table->string('portfolio')->nullable();
            $table->string('diplome')->nullable();
            $table->string('disponibilite_generale')->nullable();
            $table->text('description')->nullable();
            $table->boolean('est_verifie')->default(false);
            $table->unsignedInteger('compteur_missions_declarees')->default(0);
            $table->unsignedInteger('compteur_missions_confirmees')->default(0);
            $table->boolean('badge_orange')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artisans');
    }
};
