<?php

use App\Models\Resource;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->string('link');
            // $table->enum('type', ['Course', 'Book', 'Tool']);
            $table->string('type');
            $table->string('slug')->nullable()->unique();
            $table->string('status')->default(Resource::STATUS_UNDER_REVIEW);
            $table->text('rejection_reason')->nullable();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
