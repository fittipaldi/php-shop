<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('longitude', 11, 8);
            $table->decimal('latitude', 11, 8);
            $table->enum('status', ['open', 'close'])->comment('Options: open|close');
            $table->string('store_type');
            $table->integer('max_distance')->comment('Distance in miles');// in miles
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
