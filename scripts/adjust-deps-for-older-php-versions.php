<?php

declare(strict_types=1);

if (PHP_VERSION_ID < 80300) {
    exec(
        'composer require'
        . ' --dev'
        . ' --with-all-dependencies'
        . ' --update-with-all-dependencies'
        . ' --no-interaction'
        . ' --no-progress'
        . ' --prefer-lowest'
        . ' --ignore-platform-reqs'
        . ' symfony/cache:^6.4'
        . ' symfony/http-client:^6.4'
    );
}
