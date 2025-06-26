<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQaReportResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qa_report_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('qa_reports')->onDelete('cascade');
            $table->foreignId('rule_id')->constrained('qa_rules')->onDelete('cascade');
            $table->text('response')->nullable();
            $table->string('photo_path')->nullable();
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
        Schema::dropIfExists('qa_report_responses');
    }
}
