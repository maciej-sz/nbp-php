<?php

declare(strict_types=1);

if (PHP_VERSION_ID < 80300) {
    exec(
        'composer update'
        . ' --dev'
        . ' --with-all-dependencies'
        . ' --no-interaction'
        . ' --no-progress'
        . ' --prefer-lowest'
    );
}
