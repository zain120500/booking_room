<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meeting_memos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->text('memo');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('meeting_memos');
    }
    
};
