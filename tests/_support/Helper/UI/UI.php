<?php

namespace Helper\UI;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Module;
use Codeception\TestInterface;
use Helper\Utils;
use PHPUnit\Framework\Assert as a;

class UI extends Module
{

    public function findElements($selector)
    {
        return $this->getModule('WebDriver')->_findElements($selector);
    }

    public function findElement($selector)
    {
        $els = $this->getModule('WebDriver')->_findElements($selector);
        return count($els) > 0 ? $els[0] : null;
    }

    public function _before(TestInterface $test)
    {
        $this->getModule('WebDriver')->_capabilities(
            function ($currentCapabilities) {
                if (BROWSER === 'chrome') {
                    $currentCapabilities['chromeOptions']['prefs']['download.default_directory'] = Utils::getDownloadDirectoryPath();
                }
                return $currentCapabilities;
            }
        );
    }

    public function verifyTrue($condition)
    {
        a::assertTrue($condition);
    }

    public function verifyFalse($condition)
    {
        a::assertFalse($condition);
    }

    public function verifyEquals($expectedResult, $actualResult)
    {
        a::assertEquals($expectedResult, $actualResult);
    }

    public function verifyNotContains($value, $actual)
    {
        a::assertNotContains($value, $actual);
    }

    public function verifyContains($value, $actual)
    {
        a::assertContains($value, $actual);
    }

    public function verifyGreaterThanOrEquals($value, $actual)
    {
        a::assertGreaterThanOrEqual($value, $actual);
    }
}
