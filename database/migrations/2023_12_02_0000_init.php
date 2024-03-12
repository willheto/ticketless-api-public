<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class init extends Migration
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
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('organizationID');
            $table->string('name', 100)->default('');
            $table->string('location', 100)->default('');
            $table->string('license', 100)->default('free');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('userID');
            $table->unsignedInteger('organizationID')->nullable();
            $table->string('firstName', 100)->default('');
            $table->string('lastName', 100)->default('');
            $table->string('email', 100)->default('');
            $table->string('phoneNumber', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('userType', 100)->default('user');
            $table->string('password', 100)->default('');
            $table->integer('passwordCode')->nullable();
            $table->string('profilePicture', 100)->nullable();
            $table->string('language', 100)->default('fi');
            $table->timestamps();

            $table->foreign('organizationID')->references('organizationID')->on('organizations')->nullOnDelete();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->increments('eventID');
            $table->unsignedInteger('organizationID')->nullable();
            $table->string('name', 100)->default('');
            $table->string('location', 100)->default('');
            $table->string('type', 100)->default('');
            $table->dateTime('date')->nullable();
            $table->string('image', 100)->nullable();
            $table->boolean('isPublic')->default(true);
            $table->string('status', 100)->default('active');
            $table->string('ticketSaleUrl', 1000)->nullable();
            $table->dateTime('activeFrom')->nullable();
            $table->dateTime('activeTo')->nullable();
            $table->integer('trendingScore')->default(0);
            $table->integer('ticketMinPrice')->nullable();
            $table->integer('ticketMaxPrice')->nullable();
            $table->timestamps();

            $table->foreign('organizationID')->references('organizationID')->on('organizations')->nullOnDelete();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('ticketID');
            $table->unsignedInteger('userID');
            $table->unsignedInteger('eventID');
            $table->string('header', 100)->default('');
            $table->string('description', 200)->nullable();
            $table->integer('price')->default(0);
            $table->integer('quantity')->default(1);
            $table->boolean('requiresMembership')->default(false);
            $table->string('association', 100)->nullable();
            $table->boolean('isSelling')->default(true);
            $table->timestamps();

            $table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
            $table->foreign('eventID')->references('eventID')->on('events')->onDelete('cascade');
        });

        Schema::create('advertisements', function (Blueprint $table) {
            $table->increments('advertisementID');
            $table->string('advertiser', 100)->default('');
            $table->string('contentHtml', 10000)->default('');
            $table->boolean('isActive')->default(false);
            $table->integer('views')->default(0);
            $table->integer('clicks')->default(0);
            $table->string('redirectUrl', 1000)->default('');
            $table->string('type', 100)->default('global');
            $table->string('location', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('chats', function (Blueprint $table) {
            $table->increments('chatID');
            $table->unsignedInteger('user1ID');
            $table->unsignedInteger('user2ID');
            $table->unsignedInteger('ticketID');
            $table->boolean('isActive')->default(true);

            $table->timestamps();

            $table->foreign('user1ID')->references('userID')->on('users')->onDelete('cascade');
            $table->foreign('user2ID')->references('userID')->on('users')->onDelete('cascade');
            $table->foreign('ticketID')->references('ticketID')->on('tickets')->onDelete('cascade');
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->increments('messageID');
            $table->unsignedInteger('chatID');
            $table->unsignedInteger('senderID');
            $table->unsignedInteger('receiverID');
            $table->string('content', 1000)->default('');
            $table->boolean('isRead')->default(false);
            $table->timestamps();

            $table->foreign('chatID')->references('chatID')->on('chats')->onDelete('cascade');
            $table->foreign('receiverID')->references('userID')->on('users')->onDelete('cascade');
            $table->foreign('senderID')->references('userID')->on('users')->onDelete('cascade');
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->increments('subscriptionID');
            $table->unsignedInteger('userID');
            $table->text('endpoint')->unique(); // Push service URL
            $table->char('publicKey', 88); // User's public key
            $table->char('authToken', 24); // User's auth token
            $table->timestamps();

            $table->foreign('userID')->references('userID')->on('users')->onDelete('cascade');
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
