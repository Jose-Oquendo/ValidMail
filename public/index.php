<?php
declare(strict_types=1);

use Slim\App;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require dirname(__DIR__) . '/vendor/autoload.php';

$api = AppFactory::create();

$api->setBasePath("/services/ValMail/public");

$api->addRoutingMiddleware();

function routes(App $app){


    $app->post('/', function(Request $request, Response $response) {

        $repository = new \App\Observer;
        $data = $repository->getWelcome();

        $body = json_encode($data);
        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/data', function (Request $request, Response $response) {
  
        $repository = new \App\Observer;
        $data = $repository->getConnection('none');
        
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });
};

routes($api);

$api->addErrorMiddleware(true, true, true);
try {
    $api->run();
} catch (Exception $e) {    
  echo ( json_encode(array("status" => "failed", "message" => "This action is not allowed")) ); 
}