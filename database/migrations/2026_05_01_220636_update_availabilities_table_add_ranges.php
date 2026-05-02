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
        Schema::table('availabilities', function (Blueprint $table) {
            $table->jsonb('ranges')->nullable();
        });

        // Migrate existing data
        $availabilities = \Illuminate\Support\Facades\DB::table('availabilities')->whereNotNull('start_time')->get();
        foreach ($availabilities as $av) {
            \Illuminate\Support\Facades\DB::table('availabilities')->where('id', $av->id)->update([
                'ranges' => json_encode([
                    [
                        'start_time' => substr($av->start_time, 0, 5),
                        'end_time' => substr($av->end_time, 0, 5),
                    ]
                ])
            ]);
        }

        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availabilities', function (Blueprint $table) {
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
        });

        $availabilities = \Illuminate\Support\Facades\DB::table('availabilities')->whereNotNull('ranges')->get();
        foreach ($availabilities as $av) {
            $ranges = json_decode($av->ranges, true);
            if (!empty($ranges) && isset($ranges[0])) {
                \Illuminate\Support\Facades\DB::table('availabilities')->where('id', $av->id)->update([
                    'start_time' => $ranges[0]['start_time'] ?? null,
                    'end_time' => $ranges[0]['end_time'] ?? null,
                ]);
            }
        }

        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropColumn('ranges');
        });
    }
};
