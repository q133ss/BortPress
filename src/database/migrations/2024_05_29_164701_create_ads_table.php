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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('type_id')->constrained('types')->onDelete('cascade');
            $table->string('inventory');
            $table->json('pay_format');
            $table->foreignId('region_id')->constrained('regions')->onDelete('cascade');
            $table->unsignedBigInteger('budget');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_unique')->default(0)->comment('Уникальное для это площадки? Ставит админ');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('additional_info');
            $table->string('link');
            $table->boolean('is_offer');
            $table->boolean('is_archive')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
