<?php

use PhacMan\Process\Exception\ProcessFailedException;
use PhacMan\Process\Process;

require dirname(__DIR__, 1).'/vendor/autoload.php';

$src = dirname(__DIR__, 1).'/src';
$process = new Process(['ls', '-lsa', $src]);
$process->run();

// executes after the command finishes
if (!$process->isSuccessful()) {
    throw new ProcessFailedException($process);
}

echo $process->getOutput();

/*
Array
total 88
 4 drwxrwxr-x 4 vpv vpv  4096 окт 28 12:38 .
 4 drwxrwxr-x 8 vpv vpv  4096 окт 28 12:42 ..
 4 drwxrwxr-x 2 vpv vpv  4096 окт 28 12:38 Exception
 4 -rw-rw-r-- 1 vpv vpv  2843 окт 28 12:38 ExecutableFinder.php
 4 -rw-rw-r-- 1 vpv vpv  2424 окт 28 12:38 InputStream.php
 4 -rw-rw-r-- 1 vpv vpv  2745 окт 28 12:38 PhpExecutableFinder.php
 4 -rw-rw-r-- 1 vpv vpv  2425 окт 28 12:38 PhpProcess.php
 4 drwxrwxr-x 2 vpv vpv  4096 окт 28 12:38 Pipes
52 -rw-rw-r-- 1 vpv vpv 52007 окт 28 12:38 Process.php
 4 -rw-rw-r-- 1 vpv vpv  1961 окт 28 12:38 ProcessUtils.php
*/
