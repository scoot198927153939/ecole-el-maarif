<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('money_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // مثال: "البنك الموريتاني الإسلامي"، "بنكيلي"، "الصندوق النقدي"
            $table->enum('type', ['bank', 'mobile_app', 'cash']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('money_sources');
    }
};