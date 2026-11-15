<?php

declare(strict_types=1);

ini_set('memory_limit', '3G');

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return ECSConfig::configure()
    ->withParallel()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/tools',
        __DIR__ . '/examples',
    ])
    ->withSets([
        SetList::PSR_12,
        SetList::ARRAY,
        SetList::CLEAN_CODE,
    ])
    ->withFileExtensions(['php'])
    ->withCache('cache/ecs');
