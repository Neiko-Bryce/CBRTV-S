<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('section'); // 'about' or 'features'
            $table->string('key'); // e.g., 'title', 'subtitle', 'description'
            $table->text('value')->nullable();
            $table->json('extra')->nullable(); // For additional data like features list items
            $table->timestamps();

            $table->unique(['section', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_page_settings');
    }
};
