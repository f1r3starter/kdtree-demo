<?php

use App\Controller\{KDController, NaiveController};
use App\Model\CitiesCollection;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\{Response, Server};

require('vendor/autoload.php');

ini_set('memory_limit', '-1');

$cities = new CitiesCollection(__DIR__ . '/cities.json');
$responseMap = [
    'POST /naive' => new NaiveController($cities),
    'POST /kd' => new KDController($cities),
    'default' => static function() {
        return new Response(
            200,
            [],
            file_get_contents(__DIR__ . '/view.html')
        );
    },
];

$loop = React\EventLoop\Factory::create();

$server = new Server(
    static function (ServerRequestInterface $request) use ($responseMap) {
        $path = sprintf('%s %s', $request->getMethod(), $request->getUri()->getPath());
        $response = $responseMap[$path] ?? $responseMap['default'];

        return $response($request);
    }
);

$socket = new React\Socket\Server('0.0.0.0:8080', $loop);
$server->listen($socket);

$loop->run();
