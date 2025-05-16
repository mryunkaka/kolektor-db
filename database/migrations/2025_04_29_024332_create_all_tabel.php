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
        // Tabel Users
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id_users');
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('role')->default('user');
            $table->rememberToken()->nullable();
            $table->boolean('is_subscribed')->default(true);
            $table->dateTime('active_until')->nullable();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Tabel Vehicles
        Schema::create('vehicles', function (Blueprint $table) {
            $table->bigIncrements('id_vehicles');
            $table->string('no_kontrak')->unique(); // No Kontrak
            $table->string('nama_konsumen');        // Nama Konsumen
            $table->string('no_polisi')->unique();  // Nomor Polisi
            $table->string('no_rangka')->unique();  // Nomor Rangka
            $table->string('no_mesin')->unique();   // Nomor Mesin
            $table->string('merk_tipe');            // Merk/Type Kendaraan
            $table->integer('past_due')->default(0); // Past Due
            $table->string('nama_resort')->nullable();    // Nama Resort
            $table->string('nama_sector')->nullable();    // Nama Sector
            $table->string('nama_sub_sector')->nullable(); // Nama Sub Sector
            $table->string('product')->nullable();        // Product
            $table->timestamps();
        });


        // Tabel Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id_payments');
            $table->unsignedBigInteger('id_users');
            $table->decimal('nominal', 15, 2);
            $table->enum('duration', ['1_day', '7_days', '30_days'])->nullable();
            $table->string('status')->default('pending');
            $table->integer('unique_code')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_proof')->nullable();
            $table->string('bank_destination')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('id_users')->references('id_users')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('users');
    }
};
