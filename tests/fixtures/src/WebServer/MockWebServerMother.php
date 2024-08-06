<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Fixtures\WebServer;

use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;

class MockWebServerMother
{
    private const RESOURCES_DIR = __DIR__ . '/../../resources';

    public function create(): MockWebServer
    {
        $server = new MockWebServer();

        $di = new \RecursiveDirectoryIterator(self::RESOURCES_DIR);
        $ri = new \RecursiveIteratorIterator($di);
        $dataJsonIterator = new \RegexIterator($ri, '/\/data\.json$/i');
        /** @var \SplFileInfo $item */
        foreach ($dataJsonIterator as $item) {
            $pathName = $item->getPathname();
            $fileName = mb_substr($pathName, mb_strlen(self::RESOURCES_DIR));
            $path = dirname($fileName);

            $body = file_get_contents($pathName);
            if (false === $body) {
                throw new \RuntimeException("Cannot load resource: $pathName");
            }
            $response = new Response($body);

            $server->setResponseOfPath($path, $response);
        }

        return $server;
    }

    public function create202408WithOnlyTableA(): MockWebServer
    {
        $server = new MockWebServer();

        $tableAMarchPath = '/api/exchangerates/tables/A/2023-03-01/2023-03-31';
        $tableABody = file_get_contents(self::RESOURCES_DIR . $tableAMarchPath . '/data.json');
        assert(is_string($tableABody));
        $responseTableAMarch = new Response(
            $tableABody,
            ['Content-Type' => 'application/json'],
            200
        );

        $tableBMarchPath = '/api/exchangerates/tables/B/2023-03-01/2023-03-31';
        $responseNoData = new Response(
            '404 NotFound - Not Found - Brak danych',
            ['Content-Type' => 'text/plain'],
            404
        );

        $tableAFebruaryPath = '/api/exchangerates/tables/A/2023-02-01/2023-02-28';
        $tableBFebruaryPath = '/api/exchangerates/tables/B/2023-02-01/2023-02-28';

        $server->setResponseOfPath($tableAMarchPath, $responseTableAMarch);
        $server->setResponseOfPath("$tableAMarchPath?format=json", $responseTableAMarch);
        $server->setResponseOfPath($tableBMarchPath, $responseNoData);
        $server->setResponseOfPath("$tableBMarchPath?format=json", $responseNoData);
        $server->setResponseOfPath($tableAFebruaryPath, $responseNoData);
        $server->setResponseOfPath("$tableAFebruaryPath?format=json", $responseNoData);
        $server->setResponseOfPath($tableBFebruaryPath, $responseNoData);
        $server->setResponseOfPath("$tableBFebruaryPath?format=json", $responseNoData);

        return $server;
    }
}
