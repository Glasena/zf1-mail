<?php

require_once __DIR__ . '/bootstrap.php';

use Application\Commands\SendMailCommand;

$workers = [
    'mail' => SendMailCommand::class,
];

$name = $argv[1] ?? null;

if (!$name || !isset($workers[$name])) {
    echo "Usage: php bin/worker <name>\n";
    echo "Available Workers: " . implode(', ', array_keys($workers)) . "\n";
    exit(1);
}

(new $workers[$name]($em))->execute();