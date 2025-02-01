<?php

declare(strict_types=1);

$devDeps = [
    'symfony/cache:' . (PHP_VERSION_ID >= 80300 ? '^7.0' : '^6.2'),
    'symfony/http-client:' . (PHP_VERSION_ID >= 80300 ? '^7.0' : '^6.2'),
];

exec('composer require --dev  --no-update ' . implode(' ', $devDeps));
