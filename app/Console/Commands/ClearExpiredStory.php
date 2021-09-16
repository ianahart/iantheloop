<?php

namespace App\Console\Commands;

use App\Models\Story;
use App\Helpers\AmazonS3;
use Illuminate\Console\Command;

class ClearExpiredStory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:expiredstory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes stories that are over 24 hours old.';

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

        $result = Story::where('expire_in_unix', '<', now()->timestamp)
            ->chunkById(
                50,
                function ($stories) {

                    foreach ($stories as $story) {

                        if ($story->story_type === 'photo') {
                            $fileName = 'stories/' . $story->photo_filename;

                            $s3Bucket = new AmazonS3($fileName, null);
                            $s3Bucket->deleteFromBucket();
                        }
                        $story->delete();
                    }
                }
            );

        if ($result) {

            $this->info('Expired stories have been removed [' . date("Y-m-d H:i:s", time()) . ']');
        } else {

            $this->error('Expired stories were NOT removed, or something went wrong');
        }
    }
}
