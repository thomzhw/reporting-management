<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQaRulePhotosTable extends Migration
{
    public function up()
    {
        Schema::create('qa_rule_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained('qa_rules')->onDelete('cascade');
            $table->string('photo_path');
            $table->text('caption')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('qa_rule_photos');
    }
}