<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('owner_id');
            $table->text('content');
            $table->json('meta');

            // add fields
            $table->softDeletes();
            $table->timestamps();
        });
    }
};
