<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->json('user_data')->nullable();
            $table->foreignId('business_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null');
            $table->json('booking_time');
            $table->enum('status', ['PENDING', 'CONFIRMED', 'CANCELLED', 'EXPIRED'])->default('PENDING');
            $table->text('notes')->nullable();
            $table->timestamp('expired_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};