<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FollowSuggestion as FollowSuggestionModel;

class ReSuggestFollow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resuggest:follows';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates rejected suggestions to false after three weeks have passed.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $threeWeeks = 1814400;
        $timeElapsed = time() - $threeWeeks;

        $complete = FollowSuggestionModel::whereNotNull('rejected_at')
            ->where('rejected', '=', 1)
            ->where('rejected_at', '<', $timeElapsed)
            ->update(
                [
                    'rejected' => 0,
                    'rejected_at' => NULL,
                    'pending' => 0,
                    'suggest' => 1,
                ]
            );

        if ($complete) {
            $this->info('Follow suggestions have been resuggested successfully [' . date("Y-m-d H:i:s", time()) . ']');
        } else {
            $this->error('No updates available, or something went wrong');
        }
    }
}
