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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->String('title');
            $table->text('content');
            $table->string('slug')->nullable()->unique();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            // $table->enum('status', ['under_review', 'accepted', 'rejected'])->default('under_review');
            $table->string('status')->default('under_review');
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
