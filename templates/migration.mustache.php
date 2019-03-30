<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use {{model_path}};

class Create{{model}}sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('{{tablename}}', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
{{{fields}}}
        });
    }

    {{#down}}
    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('{{tablename}}');
    }
    {{/down}}
}
