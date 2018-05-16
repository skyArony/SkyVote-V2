<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VoteSuccess
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ip;
    public $voter_key;
    public $candidate_key;
    public $activity_key;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($ip, $voter_key, $activity_key, $candidate_key)
    {
        $this->ip = $ip;
        $this->voter_key = $voter_key;
        $this->candidate_key = $candidate_key;
        $this->activity_key = $activity_key;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
