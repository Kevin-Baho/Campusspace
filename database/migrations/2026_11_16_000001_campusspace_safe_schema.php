<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration NON-DESTRUKTIF: hanya membuat tabel/kolom yang belum ada.
 * Tidak ada drop/truncate/rename. Status disimpan sebagai string (tanpa enum native)
 * agar aman di MySQL/TiDB. Cocokkan dulu dengan skema TiDB Anda sebelum migrate.
 */
return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $t) {
                if (!Schema::hasColumn('users', 'role')) $t->string('role', 20)->default('student')->index();
                if (!Schema::hasColumn('users', 'student_id')) $t->string('student_id', 30)->nullable();
            });
        }

        if (!Schema::hasTable('facilities')) {
            Schema::create('facilities', function (Blueprint $t) {
                $t->id();
                $t->string('category', 20)->index(); // library | saung | room
                $t->string('name');
                $t->text('description')->nullable();
                $t->string('location')->nullable();
                $t->string('floor', 20)->nullable();
                $t->unsignedInteger('capacity')->nullable();
                $t->string('image_url')->nullable();
                $t->boolean('is_active')->default(true);
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('facility_units')) {
            Schema::create('facility_units', function (Blueprint $t) {
                $t->id();
                $t->foreignId('facility_id')->constrained()->cascadeOnDelete();
                $t->string('code', 40)->unique();
                $t->string('name')->nullable();
                $t->unsignedInteger('capacity')->nullable();
                $t->string('floor', 20)->nullable();
                $t->string('location')->nullable();
                $t->string('qr_token', 64)->unique();
                $t->string('status', 20)->default('available'); // available | maintenance
                $t->json('view_images')->nullable();
                $t->boolean('is_active')->default(true);
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('facility_amenities')) {
            Schema::create('facility_amenities', function (Blueprint $t) {
                $t->id();
                $t->foreignId('facility_id')->constrained()->cascadeOnDelete();
                $t->string('name');
                $t->string('icon', 40)->nullable();
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->foreignId('facility_unit_id')->constrained('facility_units')->cascadeOnDelete();
                $t->date('booking_date');
                $t->dateTime('starts_at');
                $t->dateTime('ends_at');
                $t->string('purpose', 255)->nullable();
                $t->string('status', 20)->default('confirmed')->index();
                $t->dateTime('checkin_deadline_at')->nullable();
                $t->dateTime('reminder_sent_at')->nullable();
                $t->dateTime('cancelled_at')->nullable();
                $t->timestamps();
                $t->index(['facility_unit_id', 'starts_at', 'ends_at']);
                $t->index(['user_id', 'status']);
            });
        }

        if (!Schema::hasTable('check_ins')) {
            Schema::create('check_ins', function (Blueprint $t) {
                $t->id();
                $t->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->foreignId('facility_unit_id')->constrained('facility_units')->cascadeOnDelete();
                $t->dateTime('checked_in_at');
                $t->string('ip_address', 45)->nullable();
                $t->string('user_agent', 255)->nullable();
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('booking_reviews')) {
            Schema::create('booking_reviews', function (Blueprint $t) {
                $t->id();
                $t->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->foreignId('facility_id')->constrained()->cascadeOnDelete();
                $t->unsignedTinyInteger('rating');
                $t->text('comment')->nullable();
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('ai_conversations')) {
            Schema::create('ai_conversations', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->string('title')->nullable();
                $t->timestamps();
            });
        }

        if (!Schema::hasTable('ai_messages')) {
            Schema::create('ai_messages', function (Blueprint $t) {
                $t->id();
                $t->foreignId('ai_conversation_id')->constrained('ai_conversations')->cascadeOnDelete();
                $t->string('role', 20); // user | assistant
                $t->text('content');
                $t->timestamps();
            });
        }

        // Tabel bawaan Laravel `notifications` berskema uuid; dipakai nama terpisah agar tidak bentrok.
        if (!Schema::hasTable('campus_notifications')) {
            Schema::create('campus_notifications', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->string('type', 40);
                $t->string('title');
                $t->text('message')->nullable();
                $t->unsignedBigInteger('booking_id')->nullable();
                $t->dateTime('read_at')->nullable();
                $t->timestamps();
                $t->index(['user_id', 'read_at']);
            });
        }

        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $t) {
                $t->id();
                $t->string('key', 80)->unique();
                $t->text('value')->nullable();
                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Sengaja kosong: tidak ada operasi destruktif.
    }
};
