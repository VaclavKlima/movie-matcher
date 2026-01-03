<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No-op: vote_average is created in the base movies migration.
    }

    public function down(): void
    {
        // No-op.
    }
};
