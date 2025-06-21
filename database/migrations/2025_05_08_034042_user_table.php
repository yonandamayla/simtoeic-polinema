<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->enum('role', ['student', 'lecturer', 'staff', 'alumni', 'admin']);
            $table->string('identity_number', 50)->unique();
            $table->string('password');
            $table->enum('exam_status', ['success', 'fail', 'not_yet'])->default('not_yet')->nullable();
            $table->enum('certificate_status', ['taken', 'not_taken'])->default('not_taken');
            $table->string('phone_number', 15)->nullable();
            $table->string('telegram_chat_id')->nullable();
            $table->rememberToken(); // Add remember_token column
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');  // Changed from 'user' to 'users'
    }
};
