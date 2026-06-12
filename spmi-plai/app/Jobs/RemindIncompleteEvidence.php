<?php

namespace App\Jobs;

use App\Models\AuditEvidence;
use App\Models\Period;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class RemindIncompleteEvidence implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $activePeriod = Period::where('is_active', true)->first();
        if(!$activePeriod) return;

        $prodis = User::role('prodi')->get();
        foreach ($prodis as $prodi) {
            $submittedCount = AuditEvidence::where('user_id', $prodi->id)
                ->where('period_id', $activePeriod->id)
                ->where('status', 'submitted')
                ->count();

            if ($submittedCount === 0) {
                Mail::raw('Test' . $activePeriod->name, function ($message) use ($prodi) {
                    $messsage->to($prodi->email)->subject('Reminder');
                });
            }
        }
    }
}
