<?php
namespace Itgalaxy\BinVersionCheck\Tests;

use Itgalaxy\BinVersionCheck\BinVersionCheck;
use Itgalaxy\BinVersionCheck\Exception\ConstraintException;
use Itgalaxy\BinVersionCheck\Exception\VersionParseException;
use PHPUnit\Framework\TestCase;
use Prophecy\Exception\Exception;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
            BinVersionCheck::check('php', '>=5', [
                'args' => [
                    __DIR__ . '/fixtures/test.php'
                ]
            ]);
        } catch (\Exception $exception) {}

        $this->assertNull($exception, 'Unexpected Exception');
    }

    public function testErrorIfBinIsNotString()
    {
        $this->expectException(\InvalidArgumentException::class);

        BinVersionCheck::check(['curl'], '>=1');
    }

    public function testErrorIfSemverRangeIsNotString()
    {
        $this->expectException(\InvalidArgumentException::class);

        BinVersionCheck::check('curl', ['>=1']);
    }

    public function testErrorIfOpionsIsNotArra()
    {
        $this->expectException(\InvalidArgumentException::class);

        BinVersionCheck::check('curl', '>=1', 'foobar');
    }

    public function testErrorIfBinaryNotExist()
    {
        $this->expectException(ProcessFailedException::class);

        BinVersionCheck::check('non-exist-binary-123456789', '>=1');
    }

    public function testErrorWhenTheRangeDoesNotSatisfyTheBinVersion()
    {
        $this->expectException(ConstraintException::class);

        BinVersionCheck::check('curl', '1.29.0');
    }

    public function testErrorWhenOutputNotContainVersions()
    {
        $this->expectException(VersionParseException::class);
        $this->expectExceptionMessage('Can\'t parse version');

        BinVersionCheck::check('php', '>=5', [
            'args' => __DIR__ . '/fixtures/no-version.php'
        ]);
    }

    public function testWhenOutputNotContainVersionsAndSetSafe()
    {
        $exception = null;

        try {
            BinVersionCheck::check(
                'php',
                '>=5',
                [
                    'args' => [
                        __DIR__ . '/fixtures/no-version.php'
                    ],
                    'safe' => true
                ]
            );
        } catch (\Exception $exception) {}

        $this->assertNull($exception, 'Unexpected Exception');
    }
}
