<?php
namespace filemigo\config;

enum Stage: string
{
    case develop = 'dev';
    case staging = 'stg';
    case production = 'prod';

    public static function fromString(string $name): Stage
    {
        return match ($name) {
            'develop' => self::develop,
            'staging' => self::staging,
            'production' => self::production,
        };
    }
}