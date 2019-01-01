<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../src/config/db.php';

$app = new \Slim\App;
$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");

    return $response;
});

//users route
require '../src/routes/users.php';

//logs route
require '../src/routes/logs.php';

//court route
require '../src/routes/courts.php';

//users route
require '../src/routes/user_points.php';

//rewards route
require '../src/routes/rewards.php';

//events route
require '../src/routes/events.php';

//events route
require '../src/routes/user_location.php';

//checkin/checkout route. user game schedules
require '../src/routes/schedules.php';

//version control
require '../src/routes/version.php';


require '../src/routes/send_mail.php';
$app->run();

