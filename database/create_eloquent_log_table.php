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
        Schema::create("modification_log", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->text("table");
            $table->text("table_id");
            $table->text("operation");
            $table->text("user_id")->nullable();
            $table->text("modified_state")->default("{}");
            $table->timestamp("created_at")->useCurrent();

            $table->index(["table_id", "table"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("modification_log");
    }
};
