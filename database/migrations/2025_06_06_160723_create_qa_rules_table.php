<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQaRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qa_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('qa_templates')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->boolean('requires_photo')->default(false);
            $table->string('photo_example_path')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qa_rules');
    }
}
