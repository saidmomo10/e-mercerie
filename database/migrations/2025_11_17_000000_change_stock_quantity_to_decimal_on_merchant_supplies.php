<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Note: We use raw SQL (DB::statement) to change the column type so we avoid
     * the doctrine/dbal dependency. Adjust the SQL if you use a non-MySQL driver.
     */
    public function up()
    {
        // MySQL / MariaDB syntax
        DB::statement("ALTER TABLE `merchant_supplies` MODIFY `stock_quantity` DECIMAL(10,3) NOT NULL DEFAULT 0");
    }

    /**
     * Reverse the migrations.
     *
     * Convert back to integer.
     */
    public function down()
    {
        DB::statement("ALTER TABLE `merchant_supplies` MODIFY `stock_quantity` INT NOT NULL DEFAULT 0");
    }
};
