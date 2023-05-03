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
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('color');
        });

        Schema::table('pulls', function (Blueprint $table) {
            $table->dropColumn('verdict_at');
            $table->dropForeign(['preview_id']);
            $table->dropColumn('preview_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->string('color')->nullable();
        });

        Schema::table('pulls', function (Blueprint $table) {
            $table->dateTime('verdict_at')->nullable();
            $table->boolean('comic')->default(false);
            $table->foreignId('preview_id')->nullable()->constrained('attachments');
        });
    }
};
