<?php

use App\Model\PointFactory;
use App\Controller\{KDController, NaiveController, ResponseFormatter};
use App\Model\CitiesCollection;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\{Response, Server};

require('vendor/autoload.php');

ini_set('memory_limit', '-1');

$cities = new CitiesCollection(__DIR__ . '/cities.json');
$controllersMap = [
    'POST /naive' => new NaiveController($cities),
    'POST /kd' => new KDController($cities),
    'default' => static function() {
        return new Response(
            200,
            [],
            file_get_contents(__DIR__ . '/assets/view.html')
        );
    },
];

$pointFactory = new PointFactory();
$responseFormatter = new ResponseFormatter();

$loop = React\EventLoop\Factory::create();

$server = new Server(
    static function (ServerRequestInterface $request) use ($controllersMap, $pointFactory, $responseFormatter) {
        $path = sprintf('%s %s', $request->getMethod(), $request->getUri()->getPath());
        $controller = $controllersMap[$path] ?? $controllersMap['default'];
        $point = $pointFactory->createFromRequest($request);
        $result = $controller($point);

        return $responseFormatter->prepareResponse($result);
    }
);

$socket = new React\Socket\Server('0.0.0.0:8080', $loop);
$server->listen($socket);

$loop->run();
