<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check database connection type
        $connection = DB::connection()->getDriverName();
        
        if ($connection === 'pgsql') {
            // For PostgreSQL
            DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");
        } elseif ($connection === 'mysql') {
            // For MySQL
            DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");
        } else {
            // For SQLite and others, use Schema Builder
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check database connection type
        $connection = DB::connection()->getDriverName();
        
        if ($connection === 'pgsql') {
            // For PostgreSQL
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'user'");
        } elseif ($connection === 'mysql') {
            // For MySQL
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'user'");
        } else {
            // For SQLite and others, use Schema Builder
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('user')->change();
            });
        }
    }
};
