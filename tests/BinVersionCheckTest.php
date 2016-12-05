<?php
namespace BinVersionCheck\Tests;

use BinVersionCheck\BinVersionCheck;
use PHPUnit\Framework\TestCase;

class BinVersionCheckTest extends TestCase
{
    public function testNoErrorWhenTheRangeSatisfiesTheBinVersion()
    {
        BinVersionCheck::check('curl', '>=1');
    }

    public function testNoErrorWhenTheRangeSatisfiesTheBinVersionAndOutputHaveMultipleVersions()
    {
        BinVersionCheck::check('php ' . __DIR__ . '/fixtures/test.php', '>=5');
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
        BinVersionCheck::check(
            'php ' . __DIR__ . '/fixtures/no-version.php',
            '>=5',
            [
                'safe' => true
            ]
        );
    }
}
