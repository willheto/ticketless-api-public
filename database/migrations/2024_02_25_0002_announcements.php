<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Announcements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Lumen table, required for DB job queue
         */
        Schema::create('announcements', function (Blueprint $table) {
            $table->increments('announcementID');
            $table->string('title', 100)->default('');
            $table->string('content', 200)->default('');
            $table->boolean('isActive')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // First migration. No down migration.

    }
}
