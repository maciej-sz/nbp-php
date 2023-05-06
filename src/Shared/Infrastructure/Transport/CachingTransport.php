<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Transport;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachingTransport implements Transport
{
    private const DEFAULT_LIFETIME = 'P1M';

    /** @var Transport */
    private $backend;
    /** @var CacheItemPoolInterface */
    private $cache;
    /** @var \DateInterval */
    private $lifetime;

    public function __construct(
        Transport $backend,
        CacheItemPoolInterface $cache,
        ?\DateInterval $lifetime = null
    ) {
        $this->backend = $backend;
        $this->cache = $cache;
        $this->lifetime = $lifetime ?: new \DateInterval(self::DEFAULT_LIFETIME);
    }

    public function get(string $path): array
    {
        /** @var CacheItemInterface<array<array<mixed>>> $item */
        $item = $this->cache->getItem($path);
        if (!$item->isHit()) {
            $item->set($this->backend->get($path));
            $item->expiresAfter($this->lifetime);
            $this->cache->save($item);
        }

        return $item->get();
    }
}
