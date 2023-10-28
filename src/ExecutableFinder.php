<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhacMan\Process;

use const DIRECTORY_SEPARATOR;
use function ini_get;
use const PATH_SEPARATOR;

/**
 * Generic executable finder.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ExecutableFinder
{
    private array $suffixes = ['.exe', '.bat', '.cmd', '.com'];

    /**
     * Replaces default suffixes of executable.
     */
    public function setSuffixes(array $suffixes) : void
    {
        $this->suffixes = $suffixes;
    }

    /**
     * Adds new possible suffix to check for executable.
     */
    public function addSuffix(string $suffix) : void
    {
        $this->suffixes[] = $suffix;
    }

    /**
     * Finds an executable by name.
     *
     * @param string      $name      The executable name (without the extension)
     * @param string|null $default   The default to return if no executable is found
     * @param array       $extraDirs Additional dirs to check into
     */
    public function find(string $name, string $default = null, array $extraDirs = []) : ?string
    {
        if (ini_get('open_basedir')) {
            $searchPath = array_merge(explode(PATH_SEPARATOR, ini_get('open_basedir')), $extraDirs);
            $dirs = [];
            foreach ($searchPath as $path) {
                // Silencing against https://bugs.php.net/69240
                if (@is_dir($path)) {
                    $dirs[] = $path;
                } else {
                    if (basename($path) === $name && @is_executable($path)) {
                        return $path;
                    }
                }
            }
        } else {
            if (is_string(getenv('PATH'))) {
                $str = getenv('PATH');
            } elseif (is_string(getenv('Path'))) {
                $str = getenv('Path');
            } else {
                $str = '';
            }
            $dirs = array_merge(
                explode(PATH_SEPARATOR, $str),
                $extraDirs
            );
        }

        $suffixes = [''];
        if ('\\' === DIRECTORY_SEPARATOR) {
            $pathExt = getenv('PATHEXT');
            $suffixes = array_merge($pathExt ? explode(PATH_SEPARATOR, $pathExt) : $this->suffixes, $suffixes);
        }
        foreach ($suffixes as $suffix) {
            foreach ($dirs as $dir) {
                if (@is_file($file = $dir . DIRECTORY_SEPARATOR . $name . $suffix) && ('\\' === DIRECTORY_SEPARATOR || @is_executable($file))) {
                    return $file;
                }
            }
        }

        return $default;
    }
}
