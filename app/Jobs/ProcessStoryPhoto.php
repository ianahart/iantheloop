<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Events\StoryPhotoProcessed;
use App\Models\Story;
use App\Models\User;
use App\Helpers\AmazonS3;

use Exception;

class ProcessStoryPhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $data;

    /**
     * Create a new job instance.
     * @param Array $data

     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;

        $this->onQueue('stories');
        $this->onConnection('database');
        $this->delay(now()->addSeconds(10));
        $this->afterCommit();
    }

    /**
     *
     * @return array
     */

    public function getData()
    {
        return $this->data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $story = new Story();

        $story->created_at_unix = time();
        $story->expire_in_unix = time() + 86400;

        $story->profile_id = User::find($this->data['user_id'])->profile->id;
        $story->story_user_id = $this->data['user_id'];

        foreach ($this->data as $column => $value) {

            if ($column !== 'file') {
                $story[$column] = $value;
            }
        }

        if ($this->data['story_type'] === 'photo') {


            $tmpFile = Storage::get($this->data['file']['tmp_directory'] . '/' . $this->data['file']['file_name']);

            $s3Client = new AmazonS3('stories/' . $this->data['file']['file_name'], $tmpFile);

            $s3Client->setType('story');
            $s3Client->uploadToBucket();

            $photoLink = $s3Client->downloadFromBucket();

            if (isset($photoLink) && !is_null($photoLink)) {
                $story->photo_link = $photoLink;
                $story->photo_filename = $this->data['file']['file_name'];

                Storage::disk('storage')->delete('stories/' . $this->data['file']['file_name']);
            }
        }

        if ($this->data['story_type'] === 'text') {

            $story->photo_link = NULL;
            $story->photo_filename = NULL;
        }

        $story->save();
        $story->refresh();

        $followers = !is_null($story->user->stat->followers) ? array_keys($story->user->stat->followers) : [];

        $followersOnline = collect([]);
        User::whereIn(
            'id',
            $followers
        )
            ->chunkById(100, function ($users) use ($followersOnline) {
                foreach ($users as $user) {

                    if ($user->status === 'online') {

                        $followersOnline->push($user->id);
                    }
                }
            });

        $followersOnline->push($story->user->id);

        $related = [
            'full_name' => $story->user->full_name,
            'profile_picture' => $story->profile->profile_picture,
            'followers' => $followersOnline,
        ];

        unset($story->user);
        unset($story->profile);

        StoryPhotoProcessed::dispatch($story, $related);
    }
}
