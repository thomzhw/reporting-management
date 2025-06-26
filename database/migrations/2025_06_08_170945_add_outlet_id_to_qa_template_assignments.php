<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOutletIdToQaTemplateAssignments extends Migration
{
    public function up()
    {
        Schema::table('qa_template_assignments', function (Blueprint $table) {
            $table->foreignId('outlet_id')->nullable()->after('staff_id')->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('qa_template_assignments', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn('outlet_id');
        });
    }
}