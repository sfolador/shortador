<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('events', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('url_id')->nullable(false);
            $table->string('event_type')->nullable(false);
            $table->timestamps();
            $table->foreign('url_id')->references('id')->on('urls')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
}
