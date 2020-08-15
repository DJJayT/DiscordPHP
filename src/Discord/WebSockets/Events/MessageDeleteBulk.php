<?php

/*
 * This file is apart of the DiscordPHP project.
 *
 * Copyright (c) 2016 David Cole <david@team-reflex.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Discord\WebSockets\Events;

use Discord\WebSockets\Event;
use React\Promise\Deferred;

class MessageDeleteBulk extends Event
{
    /**
     * {@inheritdoc}
     */
    public function handle(Deferred $deferred, $data)
    {
        $promises = [];

        foreach ($data->ids as $id) {
            $promise = new Deferred();
            $event = new MessageDelete($this->http, $this->factory, $this->cache, $this->discord);
            $event->handle($promise, (object) ['id' => $id, 'channel_id' => $data->channel_id, 'guild_id' => $data->guild_id]);

            $promises[] = $promise->promise();
        }

        $allPromise = \React\Promise\all($promises);
        $allPromise->then(function ($messages) use ($deferred) {
            $deferred->resolve($messages);
        });
    }
}
