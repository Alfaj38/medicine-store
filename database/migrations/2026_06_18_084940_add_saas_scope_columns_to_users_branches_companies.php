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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['management', 'company'])->default('company');
            $table->enum('data_scope', [
                'own', 'branch', 'area', 'company', 'platform', 'readonly_platform'
            ])->default('branch');
            $table->boolean('is_company_owner')->default(false);
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->enum('registration_status', ['pending', 'active', 'suspended'])->default('active');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['registration_status', 'approved_by']);
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['area_id', 'approval_status', 'approved_by', 'approved_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropColumn(['user_type', 'data_scope', 'is_company_owner', 'area_id']);
        });
    }
};
