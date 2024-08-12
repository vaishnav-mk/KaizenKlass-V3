<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('test_resources', function (Blueprint $table) {
            $table->string("content")->nullable()->change();
            $table->binary("description")->change(); // make this long blob not tinyBlob
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_resources', function (Blueprint $table) {
            //
        });
    }
};
