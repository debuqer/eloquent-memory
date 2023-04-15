<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('data_changes', function (Blueprint $table) {
            $table->id();
            $table->uuid('name')->unique();
            $table->string('type');
            $table->text('change');
            $table->timestamp('created_at')->nullable();
        });
    }
};
