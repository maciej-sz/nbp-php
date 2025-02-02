<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Transport;

use Psr\Cache\CacheItemPoolInterface;

class CachingTransportFactory implements TransportFactory
{
    private const DEFAULT_LIFETIME_INTERVAL = 'PT1H';

    /** @var CacheItemPoolInterface */
    private $cachePool;
    /** @var \DateInterval */
    private $lifetime;
    /** @var TransportFactory */
    private $backendFactory;

    public function __construct(
        CacheItemPoolInterface $cachePool,
        \DateInterval $lifetime,
        TransportFactory $backendFactory,
    ) {
        $this->cachePool = $cachePool;
        $this->lifetime = $lifetime;
        $this->backendFactory = $backendFactory;
    }

    public static function create(
        CacheItemPoolInterface $cachePool,
        ?\DateInterval $lifetime = null,
        ?TransportFactory $backendFactory = null,
    ): self {
        if (null === $lifetime) {
            $lifetime = new \DateInterval(self::DEFAULT_LIFETIME_INTERVAL);
        }
        if (null === $backendFactory) {
            $backendFactory = new HttpTransportFactory();
        }

        return new self($cachePool, $lifetime, $backendFactory);
    }

    public function make(string $baseUri): Transport
    {
        $backend = $this->backendFactory->make($baseUri);

        return CachingTransport::create($backend, $this->cachePool, $this->lifetime);
    }
}
