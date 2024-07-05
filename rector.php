<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPaths([__DIR__])
    ->withSkip([
        __DIR__ . '/.github',
        __DIR__ . '/vendor',
    ])
    ->withSets([\Rector\Set\ValueObject\DowngradeLevelSetList::DOWN_TO_PHP_74])
    // uncomment to reach your current PHP version
    ->withPhpSets();
