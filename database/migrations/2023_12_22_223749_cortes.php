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
        Schema::create('cortes', function (Blueprint $table) {
            $table->id();
            $table->string('Cajero');
            $table->decimal('Total', 10, 2);
            $table->decimal('cantidad_inicial', 10, 2);
            $table->string('Retiro');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cortes');
    }
};
