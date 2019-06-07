<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;

class EloquentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct() {
    }

    public function eloquentCreated(Model $model) {
	    \Log::info("Created Eloquent Model!");
	    \Log::info(print_r($model->toArray(),1));

	    switch(true) {
		    case is_subclass_of($model, "\App\Leads\Base"):
				\Log::info("It's a lead! Checking to see if it's a dupe...");
				if($dupe = $model->is_duplicate()) {
					\Log::info("Found possible duplicate!");
					\Log::info(print_r($dupe->toArray(),1));
					$model->duplicate_of = $dupe->id;
					$model->save();
				} else {
					\Log::info("No dupe detected...");
				}
		    	break;

	    }

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
