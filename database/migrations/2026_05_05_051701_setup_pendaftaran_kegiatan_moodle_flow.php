<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * USERS: role admin / peserta
         */
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('peserta')->after('password');
            });

            DB::table('users')
                ->where('email', 'yusup.ardabili@kemendikdasmen.go.id')
                ->update(['role' => 'admin']);
        }

        /**
         * KEGIATAN: kebutuhan flow baru
         */
        if (Schema::hasTable('kegiatan')) {
            Schema::table('kegiatan', function (Blueprint $table) {
                if (!Schema::hasColumn('kegiatan', 'jenis_pelatihan')) {
                    $table->string('jenis_pelatihan')->nullable()->after('jenis_kegiatan');
                }

                if (!Schema::hasColumn('kegiatan', 'perlu_pendaftaran')) {
                    $table->boolean('perlu_pendaftaran')->default(true)->after('jenis_pelatihan');
                }

                if (!Schema::hasColumn('kegiatan', 'moodle_course_url')) {
                    $table->string('moodle_course_url')->nullable()->after('link_zoom');
                }

                if (!Schema::hasColumn('kegiatan', 'lokasi')) {
                    $table->string('lokasi')->nullable()->after('moodle_course_url');
                }
            });
        }

        /**
         * KELAS: dipakai sebagai data pendaftaran peserta
         */
        if (!Schema::hasTable('kelas')) {
            Schema::create('kelas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('kegiatan_id')->index();

                $table->string('nama');
                $table->string('nip')->nullable();
                $table->string('nik');
                $table->string('asal_instansi');
                $table->string('email');
                $table->string('jenis_kelamin');
                $table->string('kabupaten_kota');
                $table->date('tanggal_lahir');

                $table->string('status_pendaftaran')->default('menunggu');
                $table->timestamp('moodle_injected_at')->nullable();
                $table->unsignedBigInteger('moodle_injected_by')->nullable();

                $table->timestamps();
            });
        } else {
            Schema::table('kelas', function (Blueprint $table) {
                if (!Schema::hasColumn('kelas', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->index();
                }

                if (!Schema::hasColumn('kelas', 'kegiatan_id')) {
                    $table->unsignedBigInteger('kegiatan_id')->nullable()->index();
                }

                if (!Schema::hasColumn('kelas', 'nama')) {
                    $table->string('nama')->nullable();
                }

                if (!Schema::hasColumn('kelas', 'nip')) {
                    $table->string('nip')->nullable();
                }

                if (!Schema::hasColumn('kelas', 'nik')) {
                    $table->string('nik')->nullable();
                }

                if (!Schema::hasColumn('kelas', 'asal_instansi')) {
                    $table->string('asal_instansi')->nullable();
                }

                if (!Schema::hasColumn('kelas', 'email')) {
                    $table->string('email')->nullable();
                }

                if (!Schema::hasColumn('kelas', 'jenis_kelamin')) {
                    $table->string('jenis_kelamin')->nullable();
                }

                if (!Schema::hasColumn('kelas', 'kabupaten_kota')) {
                    $table->string('kabupaten_kota')->nullable();
                }

                if (!Schema::hasColumn('kelas', 'tanggal_lahir')) {
                    $table->date('tanggal_lahir')->nullable();
                }

                if (!Schema::hasColumn('kelas', 'status_pendaftaran')) {
                    $table->string('status_pendaftaran')->default('menunggu');
                }

                if (!Schema::hasColumn('kelas', 'moodle_injected_at')) {
                    $table->timestamp('moodle_injected_at')->nullable();
                }

                if (!Schema::hasColumn('kelas', 'moodle_injected_by')) {
                    $table->unsignedBigInteger('moodle_injected_by')->nullable();
                }

                if (!Schema::hasColumn('kelas', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }

                if (!Schema::hasColumn('kelas', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kegiatan')) {
            Schema::table('kegiatan', function (Blueprint $table) {
                foreach ([
                    'jenis_pelatihan',
                    'perlu_pendaftaran',
                    'moodle_course_url',
                    'lokasi',
                ] as $column) {
                    if (Schema::hasColumn('kegiatan', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('kelas')) {
            Schema::table('kelas', function (Blueprint $table) {
                foreach ([
                    'user_id',
                    'kegiatan_id',
                    'nama',
                    'nip',
                    'nik',
                    'asal_instansi',
                    'email',
                    'jenis_kelamin',
                    'kabupaten_kota',
                    'tanggal_lahir',
                    'status_pendaftaran',
                    'moodle_injected_at',
                    'moodle_injected_by',
                ] as $column) {
                    if (Schema::hasColumn('kelas', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};