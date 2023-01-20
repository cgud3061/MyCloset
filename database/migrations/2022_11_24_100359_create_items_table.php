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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('categorie_id')->constrained('categories');
            $table->foreignId('type_id')->constrained('types');
            $table->foreignId('brand_id')->constrained('brands');
            $table->string('name', 30);
            $table->string('description');
            $table->string('image_url')->nullable();
            $table->mediuminteger('price');
            $table->boolean('state');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
};
