<?php

namespace App\Jobs;

use App\Mail\JobAlertMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendJobAlertEmailsJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected $users, $job;

    /**
     * Create a new job instance.
     */
    public function __construct($users, $job)
    {
        Log::info('constructor function' . serialize($job));
        $this->users = $users;
        $this->job = $job;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('handle function');
        foreach ($this->users as $user) {
            Log::info('Preparing to send email to: ' . $user->email);
            // Mail::to($user->email)->send(new JobAlertMail($user, $this->job));
            Mail::to('rupam.brainium@gmail.com')->send(new JobAlertMail($user, $this->job));
        }
    }
}
