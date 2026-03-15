<?php

namespace App\Services\Channel;

use Illuminate\Support\Facades\Log;

class EventBuffer
{
    private static $buffer = [];

    public static function push(string $channel, int $id ,array $event)
    {
        self::$buffer[$channel][$id][] = $event;

    }

    public static function popAll(string $channel, $id): array
    {
        if (!isset(self::$buffer[$channel][$id])) {
            return [];
        }
        $events = self::$buffer[$channel][$id];

        // Clear buffer after reading
        self::$buffer[$channel][$id] = [];

        return $events;
    }
}
