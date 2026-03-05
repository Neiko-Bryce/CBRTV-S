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
        Schema::create('archived_elections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_election_id')->unique();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('archived_by')->nullable();
            $table->string('election_id')->nullable();
            $table->string('election_name');
            $table->string('type_of_election')->nullable();
            $table->text('description')->nullable();
            $table->string('venue')->nullable();
            $table->date('election_date')->nullable();
            $table->time('timestarted')->nullable();
            $table->time('time_ended')->nullable();
            $table->string('status')->default('completed');
            $table->boolean('show_live_results')->default(false);
            $table->timestamp('archived_at');
            $table->timestamps();

            $table->index(['school_id', 'archived_at']);
            $table->index(['status', 'archived_at']);
            $table->index(['show_live_results']);
            $table->index(['organization_id']);

            $table->foreign('school_id')->references('id')->on('schools')->nullOnDelete();
            $table->foreign('organization_id')->references('id')->on('organizations')->nullOnDelete();
            $table->foreign('archived_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('archived_partylists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_partylist_id')->nullable();
            $table->unsignedBigInteger('archived_election_id');
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['archived_election_id']);
            $table->index(['school_id']);
            $table->index(['organization_id']);
            $table->index(['original_partylist_id']);

            $table->foreign('archived_election_id')->references('id')->on('archived_elections')->cascadeOnDelete();
            $table->foreign('school_id')->references('id')->on('schools')->nullOnDelete();
            $table->foreign('organization_id')->references('id')->on('organizations')->nullOnDelete();
        });

        Schema::create('archived_candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_candidate_id')->nullable();
            $table->unsignedBigInteger('archived_election_id');
            $table->unsignedBigInteger('archived_partylist_id')->nullable();
            $table->unsignedBigInteger('original_position_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->string('position_name')->nullable();
            $table->integer('position_order')->default(0);
            $table->integer('number_of_slots')->default(1);
            $table->string('candidate_name');
            $table->string('photo')->nullable();
            $table->text('biography')->nullable();
            $table->text('platform')->nullable();
            $table->integer('votes_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['archived_election_id']);
            $table->index(['archived_partylist_id']);
            $table->index(['school_id']);
            $table->index(['organization_id']);
            $table->index(['original_candidate_id']);
            $table->index(['original_position_id']);

            $table->foreign('archived_election_id')->references('id')->on('archived_elections')->cascadeOnDelete();
            $table->foreign('archived_partylist_id')->references('id')->on('archived_partylists')->nullOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->nullOnDelete();
            $table->foreign('school_id')->references('id')->on('schools')->nullOnDelete();
            $table->foreign('organization_id')->references('id')->on('organizations')->nullOnDelete();
        });

        Schema::create('archived_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_vote_id')->nullable();
            $table->unsignedBigInteger('archived_election_id');
            $table->unsignedBigInteger('archived_candidate_id');
            $table->unsignedBigInteger('voter_id')->nullable();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->timestamp('voted_at')->nullable();
            $table->timestamps();

            $table->index(['archived_election_id']);
            $table->index(['archived_candidate_id']);
            $table->index(['voter_id']);
            $table->index(['school_id']);
            $table->index(['organization_id']);
            $table->index(['original_vote_id']);
            $table->index(['voted_at']);

            $table->foreign('archived_election_id')->references('id')->on('archived_elections')->cascadeOnDelete();
            $table->foreign('archived_candidate_id')->references('id')->on('archived_candidates')->cascadeOnDelete();
            $table->foreign('voter_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('school_id')->references('id')->on('schools')->nullOnDelete();
            $table->foreign('organization_id')->references('id')->on('organizations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archived_votes');
        Schema::dropIfExists('archived_candidates');
        Schema::dropIfExists('archived_partylists');
        Schema::dropIfExists('archived_elections');
    }
};
