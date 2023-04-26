<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Fixtures\WebServer;

use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;

class MockWebServerFactory
{
    public function create(): MockWebServer
    {
        $server = new MockWebServer();

        $resourcesDir = __DIR__ . '/../../resources';
        $di = new \RecursiveDirectoryIterator($resourcesDir);
        $ri = new \RecursiveIteratorIterator($di);
        $dataJsonIterator = new \RegexIterator($ri, '/\/data\.json$/i');
        /** @var \SplFileInfo $item */
        foreach ($dataJsonIterator as $item) {
            $pathName = $item->getPathname();
            $fileName = mb_substr($pathName, mb_strlen($resourcesDir));
            $path = dirname($fileName);

            $response = new Response(file_get_contents($pathName));

            $server->setResponseOfPath($path, $response);
        }

        return $server;
    }
}
