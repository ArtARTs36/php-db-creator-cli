<?php

require_once 'vendor/autoload.php';
require_once 'cli.php';

$arguments = array_slice(array_map('trim', $_SERVER['argv']), 1);
$firstArg = $arguments[0];

/** @var array<string, \Closure> $commands */
$commands = [
    'dump-env'   => Closure::fromCallable('ArtARTs36\DbCreator\Cli\dump_env_file'),
    'help'       => Closure::fromCallable('ArtARTs36\DbCreator\Cli\show_help'),
    'locate-env' => Closure::fromCallable('ArtARTs36\DbCreator\Cli\locate_env'),
    'create'     => Closure::fromCallable('ArtARTs36\DbCreator\Cli\create_database'),
];

if (! array_key_exists($firstArg, $commands)) {
    throw new \LogicException('Command not found!');
}

$command = $commands[$firstArg];

$command(array_slice($arguments, 1));

echo "\n";
