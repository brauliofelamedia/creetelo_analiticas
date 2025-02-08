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
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->default('default.png')->nullable();
            $table->string('color_primary')->default('#000000')->nullable();
            $table->string('color_secundary')->default('#000000')->nullable();
            $table->text('code')->nullable();

            //Extra token
            $table->text('company_id')->nullable();
            $table->text('location_id')->nullable();
            $table->text('refresh_token')->nullable();
            $table->text('access_token')->nullable();
            $table->string('base_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configs');
    }
};
