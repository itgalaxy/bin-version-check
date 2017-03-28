<?php
namespace Itgalaxy\BinVersionCheck\Tests;

use Itgalaxy\BinVersionCheck\BinVersionCheck;
use PHPUnit\Framework\TestCase;

class BinVersionCheckTest extends TestCase
{
    public function testNoErrorWhenTheRangeSatisfiesTheBinVersion()
    {
        $exception = null;

        try {
            BinVersionCheck::check('curl', '>=1');
        } catch (\Exception $exception) {}

        $this->assertNull($exception, 'Unexpected Exception');
    }

    public function testNoErrorWhenTheRangeSatisfiesTheBinVersionAndOutputHaveMultipleVersions()
    {
        $exception = null;

        try {
            BinVersionCheck::check('php ' . __DIR__ . '/fixtures/test.php', '>=5');
        } catch (\Exception $exception) {}

        $this->assertNull($exception, 'Unexpected Exception');
    }

    public function testErrorIfBinIsNotString()
    {
        $this->expectException(\Exception::class);

        BinVersionCheck::check(['curl'], '>=1');
    }

    public function testErrorIfSemverRangeIsNotString()
    {
        $this->expectException(\Exception::class);

        BinVersionCheck::check('curl', ['>=1']);
    }

    public function testErrorIfBinaryNotExist()
    {
        $this->expectException(\Exception::class);

        BinVersionCheck::check('non-exist-binary-123456789', '>=1');
    }

    public function testErrorWhenTheRangeDoesNotSatisfyTheBinVersion()
    {
        $this->expectException(\Exception::class);

        BinVersionCheck::check('curl', '1.29.0');
    }

    public function testErrorWhenOutputNotContainVersions()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can\'t parse version');

        BinVersionCheck::check('php ' . __DIR__ . '/fixtures/no-version.php', '>=5');
    }

    public function testWhenOutputNotContainVersionsAndSetSafe()
    {
        $exception = null;

        try {
            BinVersionCheck::check(
                'php ' . __DIR__ . '/fixtures/no-version.php',
                '>=5',
                [
                    'safe' => true
                ]
            );
        } catch (\Exception $exception) {}

        $this->assertNull($exception, 'Unexpected Exception');
    }
}
