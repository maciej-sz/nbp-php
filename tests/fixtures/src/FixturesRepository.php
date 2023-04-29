<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Fixtures;

class FixturesRepository
{
    public function fetchJson(string $fixturePath): string
    {
        $contents = file_get_contents($this->getFullFixturePath($fixturePath, 'json'));
        if (false === $contents) {
            throw new \Exception("Cannot read fixture file: {$fixturePath}");
        }

        return $contents;
    }

    public function fetchArray(string $fixturePath): array
    {
        return json_decode($this->fetchJson($fixturePath), true);
    }

    public function getFullFixturePath(string $basePath, string $fileExt): string
    {
        $expectedFixturesResourceDir = __DIR__ . '/../resources';
        $fixturesResourceDir = realpath($expectedFixturesResourceDir);
        if (false === $fixturesResourceDir) {
            throw new \Exception("Fixtures resource dir is missing. Expected at: {$expectedFixturesResourceDir}");
        }

        $basePath = ltrim($basePath, '/');
        $expectedFixtureFullPath =
            $fixturesResourceDir
            . "/{$basePath}"
            . ((!empty($fileExt)) ? ".{$fileExt}" : '')
        ;
        $fixtureFullPath = realpath($expectedFixtureFullPath);
        if (false === $fixtureFullPath) {
            throw new \Exception("Fixture file not found. Searched at: {$expectedFixtureFullPath}");
        }
        if (strpos($fixtureFullPath, $fixturesResourceDir) !== 0) {
            throw new \Exception("Security breach: fixture file path cannot go above fixtures directory");
        }

        return $fixtureFullPath;
    }
}
