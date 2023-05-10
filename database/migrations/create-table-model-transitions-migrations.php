<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('model_transitions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->json('parameters');
            $table->uuid('batch');
            $table->timestamps();
        });
    }
};
