<?php

namespace Tests;

use PHPUnit\Framework\Assert;

/**
 * Trait TestHelpersTrait
 * @package Tests
 */
trait TestHelpersTrait
{
    /**
     * @param  null  $filename
     * @param  null  $needle
     */
    public function assertFileContainsString($filename = null, $needle = null)
    {
        if (file_exists($filename)) {
            $data = file_get_contents($filename);
            Assert::assertStringContainsString($needle, $data);
        } else {
            Assert::assertTrue(false);
        }
    }

    /**
     * @param  null  $filename
     * @param  null  $needle
     */
    public function assertFileNotContainString($filename = null, $needle = null)
    {
        if (file_exists($filename)) {
            $data = file_get_contents($filename);
            Assert::assertStringNotContainsString($needle, $data);
        } else {
            Assert::assertTrue(false);
        }
    }

    /**
     * @param  null  $filename
     * @param  null  $needle
     */
    public function assertFileNotContainsString($filename = null, $needle = null)
    {
        if (file_exists($filename)) {
            $data = file_get_contents($filename);
            Assert::assertStringNotContainsString($needle, $data);
        } else {
            Assert::assertTrue(false);
        }
    }

    /**
     * @return string|string[]|null
     */
    public function joinPath()
    {
        $paths = array();

        foreach (func_get_args() as $arg) {
            if ($arg !== '') {
                $paths[] = $arg;
            }
        }

        return preg_replace('#/+#', '/', join('/', $paths));
    }

    /**
     * @return string|string[]|null
     */
    public function pathJoin()
    {
        $paths = array();

        foreach (func_get_args() as $arg) {
            if ($arg !== '') {
                $paths[] = $arg;
            }
        }

        return preg_replace('#/+#', '/', join('/', $paths));
    }

    /**
     * @param $migrationName
     */
    public function assertMigrationFileExists($migrationName)
    {
        $migrationPath = database_path() . "/migrations";
        foreach (scandir($migrationPath) as $filename) {
            if (strpos($filename, $migrationName)) {
                Assert::assertTrue(true);
                return $filename;
            }
        }

        Assert::assertTrue(false);
    }
}
