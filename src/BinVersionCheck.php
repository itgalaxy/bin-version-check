<?php
namespace Itgalaxy\BinVersionCheck;

use Composer\Semver\Semver;
use Itgalaxy\BinVersionCheck\Exception\ConstraintException;
use Itgalaxy\BinVersionCheck\Exception\VersionParseException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class BinVersionCheck
{
    public static function check($bin, $semverRange, $options = [])
    {
        if (!is_string($bin)) {
            throw new \InvalidArgumentException('Option `binary` should be string');
        }

        if (!is_string($semverRange)) {
            throw new \InvalidArgumentException('Option `semverRange` should be string');
        }

        if (!is_array($options)) {
            throw new \InvalidArgumentException('Option `options` should be array');
        }

        $args = !empty($options) && !empty($options['args']) ? (array) $options['args'] : ['--version'];

        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($bin)
            ->setArguments($args)
            ->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        $version = self::findVersions($output, $options);

        if (!Semver::satisfies($version, $semverRange)) {
            throw new ConstraintException($bin . ' doesn\'t satisfy the version requirement of ' . $semverRange);
        }
    }

    private static function findVersions($str, $options = [])
    {
        preg_match('{v?(\d{1,5})(\.\d++)?(\.\d++)?(\.\d++)?}', $str, $matches);

        if (!empty($matches)) {
            $version = $matches[1]
                . (!empty($matches[2]) ? $matches[2] : '.0')
                . (!empty($matches[3]) ? $matches[3] : '.0')
                . (!empty($matches[4]) ? $matches[4] : '.0');

            return $version;
        }

        $safe = !empty($options) && !empty($options['safe']) ? (bool) $options['safe'] : false;

        if (!$safe) {
            throw new VersionParseException('Can\'t parse version');
        }

        return '99999.99999.99999';
    }
}
