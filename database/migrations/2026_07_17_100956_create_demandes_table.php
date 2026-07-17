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
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('artisan_id')->constrained('artisans')->onDelete('cascade');
            $table->enum('contact_canal',['whatsapp', 'email', 'appel']);
            $table->enum('statut_client', ['en_attante', 'oui', 'non'])->default('en_attante');
            $table->enum('statut_artisan', ['en_attante', 'oui', 'non'])->default('en_attante');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
