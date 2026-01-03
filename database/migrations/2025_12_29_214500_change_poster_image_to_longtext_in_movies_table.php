<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // No-op: poster_url stores remote URLs, no column changes needed.
    }

    public function down()
    {
        // No-op.
    }
};
