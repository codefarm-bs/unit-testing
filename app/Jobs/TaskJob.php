<?php

namespace App\Jobs;

use App\Mail\TaskMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class TaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach (User::all() as $user) {
            $user->tasks()
                ->where('ended_at', '<=',  now())
                ->get()
                ->each(function ($task) use ($user) {
                    Mail::to($user->email)->send(new TaskMail($task));
                    $task->update(['ended_at' => null]);
                });
        }
    }
}
