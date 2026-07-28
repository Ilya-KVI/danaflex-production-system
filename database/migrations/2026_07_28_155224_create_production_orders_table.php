<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('production_orders', function (Blueprint $table) {

            $table->id();


            $table->string('customer_name');


            $table->string('material');


            $table->integer('quantity');


            $table->string('status')
                ->default('Новый');


            // Связь с производственной линией
            $table->foreignId('production_line_id')
                ->nullable()
                ->constrained('production_lines')
                ->nullOnDelete();


            $table->timestamps();

        });
    }



    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }

};
