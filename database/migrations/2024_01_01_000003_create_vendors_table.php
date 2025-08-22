<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('bio')->nullable();
            $table->string('location');
            $table->json('location_data')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('cover')->nullable();
            $table->foreignId('category_id')->constrained();
            $table->decimal('ratings', 3, 2)->default(0);
            $table->integer('reviews')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendors');
    }
};