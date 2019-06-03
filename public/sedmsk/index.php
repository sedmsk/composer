<?php

function getBody(): array
{
    static $body;

    if ($body === null) {
        $body = json_decode(file_get_contents('php://input') ?: '[]', true);
    }

    return $body;
}

$cmd = [
    'cd '.implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..']),
    'git pull',
    'composer install',
    'git add .',
    'git commit -S -m '.escapeshellarg(trim('Update '.(getBody()['repository']['full_name'] ?? ''))),
    'git push'
];

fastcgi_finish_request();

exec(implode(';', $cmd), $output);

file_put_contents(implode(DIRECTORY_SEPARATOR, [
    __DIR__,
    '..',
    '..',
    'logs',
    time().rand(1000, 9999).'.json',
]), json_encode($output, JSON_PRETTY_PRINT));
