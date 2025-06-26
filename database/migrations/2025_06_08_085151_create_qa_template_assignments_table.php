<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQaTemplateAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qa_template_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('qa_templates')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('due_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'overdue', 'pending_review'])
                  ->default('pending');
            $table->longText('notes');
            $table->timestamps();
            
            // Optional: Add a unique constraint to prevent duplicate assignments
            $table->unique(['template_id', 'staff_id', 'status'], 'unique_active_assignment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qa_template_assignments');
    }
}
