<?php

namespace HappyHorizon\GA\MeasurementProtocol\Models\Events;

class AbstractEventModel
{
    protected static string $eventName;

    public static function eventName(): string
    {
        return static::$eventName;
    }
}