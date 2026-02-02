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
        Schema::table('elections', function (Blueprint $table) {
            $table->string('status')->default('upcoming')->after('time_ended');
        });

        // Update existing elections with calculated status
        $elections = \App\Models\Election::all();
        foreach ($elections as $election) {
            if (empty($election->status)) {
                $now = \Carbon\Carbon::now('Asia/Manila');
                $electionDate = \Carbon\Carbon::parse($election->election_date, 'Asia/Manila');

                $status = 'upcoming';
                if (! empty($election->timestarted)) {
                    try {
                        $electionDateTime = \Carbon\Carbon::createFromFormat(
                            'Y-m-d H:i:s',
                            $election->election_date.' '.$election->timestarted.':00',
                            'Asia/Manila'
                        );
                    } catch (\Exception $e) {
                        $electionDateTime = $electionDate->copy()->startOfDay();
                    }
                } else {
                    $electionDateTime = $electionDate->copy()->startOfDay();
                }

                if (! empty($election->time_ended)) {
                    try {
                        $endDateTime = \Carbon\Carbon::createFromFormat(
                            'Y-m-d H:i:s',
                            $election->election_date.' '.$election->time_ended.':00',
                            'Asia/Manila'
                        );
                        if ($now->greaterThanOrEqualTo($endDateTime)) {
                            $status = 'completed';
                        } elseif ($now->greaterThanOrEqualTo($electionDateTime)) {
                            $status = 'ongoing';
                        }
                    } catch (\Exception $e) {
                        if ($now->greaterThanOrEqualTo($electionDateTime)) {
                            $status = 'ongoing';
                        }
                    }
                } elseif ($now->greaterThanOrEqualTo($electionDateTime)) {
                    $status = 'ongoing';
                }

                $election->update(['status' => $status]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
