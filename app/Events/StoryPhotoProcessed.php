<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Story;

class StoryPhotoProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Story $story;
    public array $related;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Story $story, array $related)
    {
        $this->story = $story;
        $this->related = $related;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'data' => [
                'story' => $this->story,
                'profile_picture' => $this->related['profile_picture'],
                'full_name' => $this->related['full_name'],
            ],
        ];
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {

        $channels = [];

        foreach ($this->related['followers'] as $follower) {
            $channels[] = new PrivateChannel('stories.' . $follower);
        }
        return $channels;
    }
}
