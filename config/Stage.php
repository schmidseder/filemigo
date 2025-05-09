<?php
/**
 * Filemigo
 * Copyright (c) 2025 Christian Schmidseder
 *
 * This file is part of Filemigo.
 *
 * Licensed under the MIT License. See the LICENSE file
 * in the project root for full license information.
 */
 
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