<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('kegiatan', 'lokasi')) {
            Schema::table('kegiatan', function (Blueprint $table) {
                $table->dropColumn('lokasi');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('kegiatan', 'lokasi')) {
            Schema::table('kegiatan', function (Blueprint $table) {
                $table->string('lokasi')->nullable()->after('moodle_course_url');
            });
        }
    }
};