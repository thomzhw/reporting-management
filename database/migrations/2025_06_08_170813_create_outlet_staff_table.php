<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutletStaffTable extends Migration
{
    public function up()
    {
        Schema::create('outlet_staff', function (Blueprint $table) {
            $table->id(); // Add auto-incrementing primary key
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->string('role');
            $table->foreignId('assigned_by')->constrained('users');
            $table->timestamps();
            $table->unique(['outlet_id', 'staff_id']); // Prevent duplicate assignments
        });
    }

    public function down()
    {
        Schema::dropIfExists('outlet_staff');
    }
}