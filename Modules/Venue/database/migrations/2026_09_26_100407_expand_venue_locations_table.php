<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venue_locations', function (Blueprint $table) {
            $table->renameColumn('name', 'city');
            $table->string('district')->nullable()->after('slug');
            $table->string('state')->nullable()->after('district');
        });
    }

    public function down(): void
    {
        Schema::table('venue_locations', function (Blueprint $table) {
            $table->dropColumn(['district', 'state']);
            $table->renameColumn('city', 'name');
        });
    }
};
