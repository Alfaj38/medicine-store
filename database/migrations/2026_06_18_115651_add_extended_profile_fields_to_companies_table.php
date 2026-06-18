<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('website')->nullable();
            $table->string('alternative_phone')->nullable();
            $table->string('business_type')->nullable();
            $table->integer('year_established')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('trade_license_number')->nullable();
            $table->string('vat_registration_number')->nullable();
            $table->text('about_company')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'website',
                'alternative_phone',
                'business_type',
                'year_established',
                'registration_number',
                'tax_id',
                'trade_license_number',
                'vat_registration_number',
                'about_company'
            ]);
        });
    }
};
