<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOutletIdToQaTemplates extends Migration
{
    public function up()
    {
        Schema::table('qa_templates', function (Blueprint $table) {
            $table->foreignId('outlet_id')->nullable()->constrained()->after('head_id');
            $table->string('category')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('qa_templates', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn(['outlet_id', 'category']);
        });
    }
}