<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Notifications\Interaction;

class ProcessInteraction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $interaction;
    protected User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $interaction, User $user)
    {
        $this->interaction = $interaction;
        $this->user = $user;
        $this->onQueue('interactions');
        $this->onConnection('database');
        $this->delay(now()->addMinutes(1));
        $this->afterCommit();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->notify(new Interaction($this->interaction));
    }
}
