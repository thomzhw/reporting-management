<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReviewFieldsToQaReportsTable extends Migration
{
    public function up()
    {
        Schema::table('qa_reports', function (Blueprint $table) {
            $table->timestamp('reviewed_at')->nullable()->after('completed_at');
            $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending_review')->after('reviewed_by');
            $table->text('feedback')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('qa_reports', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['reviewed_at', 'reviewed_by', 'status', 'feedback']);
        });
    }
}