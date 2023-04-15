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
        Schema::table('application_types', function (Blueprint $table) {
            $table->integer('category_id')->nullable();
            $table->text('description')->nullable();
            $table->foreign('category_id')->references('id')->on('application_categories')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('application_types', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
};
