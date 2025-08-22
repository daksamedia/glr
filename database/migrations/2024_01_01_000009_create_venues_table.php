<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('images')->nullable();
            $table->integer('large_num')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('composition')->nullable();
            $table->boolean('electricity')->default(false);
            $table->boolean('parking_lot')->default(false);
            $table->integer('rooms_num')->nullable();
            $table->integer('toilets_num')->nullable();
            $table->boolean('prayer_room')->default(false);
            $table->string('location');
            $table->boolean('available_status')->default(true);
            $table->decimal('price', 10, 2);
            $table->decimal('ratings', 3, 2)->default(0);
            $table->integer('reviews')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('venues');
    }
};