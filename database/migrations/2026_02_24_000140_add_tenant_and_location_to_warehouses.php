<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouses', 'created_by')) {
                $table->foreignId('created_by')->after('id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('warehouses', 'location')) {
                $table->string('location')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            if (Schema::hasColumn('warehouses', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
            if (Schema::hasColumn('warehouses', 'location')) {
                $table->dropColumn('location');
            }
        });
    }
};

