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
		switch(true) {
			case is_subclass_of($model, "\App\Leads\Base"):
				if($dupe = $model->is_duplicate()) {
					\Log::info("Found possible duplicate!");
					\Log::info(print_r($dupe->toArray(),1));
					$model->duplicate_of = $dupe->id;
					$model->save();
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
