<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhacMan\Process;

use PHPUnit\Framework\TestCase;
use PhacMan\Process\Exception\LogicException;
use PhacMan\Process\PhpExecutableFinder;
use PhacMan\Process\PhpProcess;
use const PHP_SAPI;
use const PHP_VERSION;

class PhpProcessTest extends TestCase
{
    public function testNonBlockingWorks()
    {
        $expected = 'hello world!';
        $process = new PhpProcess(<<<PHP
<?php echo '$expected';
PHP
        );
        $process->start();
        $process->wait();
        $this->assertEquals($expected, $process->getOutput());
    }

    public function testCommandLine()
    {
        $process = new PhpProcess(<<<'PHP'
<?php echo phpversion().PHP_SAPI;
PHP
        );

        $commandLine = $process->getCommandLine();

        $process->start();
        $this->assertStringContainsString($commandLine, $process->getCommandLine(), '::getCommandLine() returns the command line of PHP after start');

        $process->wait();
        $this->assertStringContainsString($commandLine, $process->getCommandLine(), '::getCommandLine() returns the command line of PHP after wait');

        $this->assertSame(PHP_VERSION. PHP_SAPI, $process->getOutput());
    }

    public function testPassingPhpExplicitly()
    {
        $finder = new PhpExecutableFinder();
        $php = array_merge([$finder->find(false)], $finder->findArguments());

        $expected = 'hello world!';
        $script = <<<PHP
<?php echo '$expected';
PHP;
        $process = new PhpProcess($script, null, null, 60, $php);
        $process->run();
        $this->assertEquals($expected, $process->getOutput());
    }

    public function testProcessCannotBeCreatedUsingFromShellCommandLine()
    {
        static::expectException(LogicException::class);
        static::expectExceptionMessage('The "PhacMan\Process\PhpProcess::fromShellCommandline()" method cannot be called when using "PhacMan\Process\PhpProcess".');
        PhpProcess::fromShellCommandline(<<<PHP
<?php echo 'Hello World!';
PHP
        );
    }
}
