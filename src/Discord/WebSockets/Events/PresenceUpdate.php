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

use Discord\Parts\WebSockets\PresenceUpdate as PresenceUpdatePart;
use Discord\WebSockets\Event;
use React\Promise\Deferred;

class PresenceUpdate extends Event
{
    /**
     * {@inheritdoc}
     */
    public function handle(Deferred $deferred, $data)
    {
        /**
         * @var PresenceUpdatePart
         */
        $presence = $this->factory->create(PresenceUpdatePart::class, $data, true);

        if ($guild = $presence->guild) {
            if ($member = $presence->member) {
                $oldPresence = $member->updateFromPresence($presence);

                $guild->members->push($member);
                $this->discord->guilds->push($guild);

                $deferred->resolve([$presence, $oldPresence]);
            }
        }

        $deferred->resolve($presence);
    }
}
