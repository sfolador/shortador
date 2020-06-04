<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('urls', static function (Blueprint $table) {
            $table->id();
            $table->string('url')->nullable(false);
            $table->string('shortened')->nullable(false);
            $table->timestamps();
            $table->index('shortened');
            $table->unique('url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('urls');
    }
}
