<?php

namespace App\Events;

use App\Model\Goods;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Collection;
class GoodsChange
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $goods;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Goods $goods=null,Collection $collection=null)
    {
        //

        $this->goods=$goods?$goods:$collection;
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
