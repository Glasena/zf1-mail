<?php

$envFile = __DIR__ . '/../.env';

if (!file_exists($envFile)) {
    return;
}

foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);

    if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
        continue;
    }

    [$key, $value] = explode('=', $line, 2);

    $key   = trim($key);
    $value = trim($value);

    if (!getenv($key)) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}
