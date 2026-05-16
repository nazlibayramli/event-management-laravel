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
    // Events Table
    Schema::create('events', function ($table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
        $table->string('title');
        $table->text('description');
        $table->string('location');
        $table->dateTime('date');
        $table->timestamps();
    });

    // Ticket Categories Table
    Schema::create('ticket_categories', function ($table) {
        $table->id();
        $table->foreignId('event_id')->constrained()->onDelete('cascade');
        $table->string('name'); 
        $table->decimal('price', 10, 2);
        $table->integer('capacity');
        $table->timestamps();
    });

    // Orders Table
    Schema::create('orders', function ($table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->decimal('total_amount', 10, 2);
        $table->string('status')->default('paid'); 
        $table->timestamps();
    });

    // Tickets Table
    Schema::create('tickets', function ($table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->onDelete('cascade');
        $table->foreignId('ticket_category_id')->constrained()->onDelete('cascade');
        $table->string('seat_number');
        $table->string('qr_code')->unique();
        $table->boolean('is_used')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_management_tables');
    }
};
