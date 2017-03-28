<?php
namespace Itgalaxy\BinVersionCheck;

use Composer\Semver\Semver;

class BinVersionCheck
{
    public static function check($bin, $semverRange, $options = [])
    {
        if (!is_string($bin)) {
            throw new \Exception('Options `binary` should be string');
        }

        if (!is_string($semverRange)) {
            throw new \Exception('Options `semverRange` should be string');
        }

        $args = !empty($options) && !empty($options['args']) ? $options['args'] : ['--version'];

        exec($bin . ' ' . implode(' ', $args), $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception($bin . ' return ' . $returnVar . ' exit code');
        }

        $version = self::findVersions(implode(' ', $output), $options);

        if (!Semver::satisfies($version, $semverRange)) {
            throw new \Exception($bin . ' doesn\'t satisfy the version requirement of ' . $semverRange);
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
        } else {
            $safe = !empty($options) && !empty($options['safe']) ? $options['safe'] : false;

            if (!$safe) {
                throw new \Exception('Can\'t parse version');
            }
        }

        return '99999.99999.99999';
    }
}
