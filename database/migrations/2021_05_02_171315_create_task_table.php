<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('user_id')->nullable();
            $table->string('party_name')->nullable();
            $table->text('description')->nullable();
            $table->date('target_date')->nullable();
            $table->text('attechment')->nullable();
            $table->enum('status', ['pending', 'complated', 'deleted'])->default('pending');
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task');
    }
}
