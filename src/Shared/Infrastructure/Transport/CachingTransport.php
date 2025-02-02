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
    private $cachePool;
    /** @var \DateInterval */
    private $lifetime;

    public function __construct(
        Transport $backend,
        CacheItemPoolInterface $cachePool,
        \DateInterval $lifetime,
    ) {
        $this->backend = $backend;
        $this->cachePool = $cachePool;
        $this->lifetime = $lifetime;
    }

    public static function create(
        Transport $backend,
        CacheItemPoolInterface $cachePool,
        ?\DateInterval $lifetime = null,
    ): self {
        return new self(
            $backend,
            $cachePool,
            $lifetime ?: new \DateInterval(self::DEFAULT_LIFETIME)
        );
    }

    public function get(string $path): array
    {
        /** @var CacheItemInterface<array<array<mixed>>> $item */
        $item = $this->cachePool->getItem($path);
        if (!$item->isHit()) {
            $item->set($this->backend->get($path));
            $item->expiresAfter($this->lifetime);
            $this->cachePool->save($item);
        }

        return $item->get();
    }
}
