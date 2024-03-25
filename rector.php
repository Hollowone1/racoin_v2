<?php

use Rector\Config\RectorConfig;

return function (RectorConfig $config): void {
    $config->rule(\Rector\Rector\Class_\AbstractClassToInterfaceRector::class);
    $config->rule(\Rector\Rector\Class_\AnonymousClassToTraitRector::class);

    $config->paths([
        __DIR__ . '/app/src',
    ]);
};
