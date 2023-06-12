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
            $table->string('address', 32);
            $table->string('model_class');
            $table->json('properties');
            $table->uuid('batch');
            $table->timestamps();

            $table->index('address');
        });
    }
};
