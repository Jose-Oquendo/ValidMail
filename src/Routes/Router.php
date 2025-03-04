<?php
declare(strict_types=1);

namespace Routes;

use Slim\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy;

use App\Observer;
use App\Catcher;
use App\Mailer;

class Router
{
    private $app;
    private $observer;

    public function __construct(App $api)
    {
        $this->app = $api;
    }

    public function jsonResponse(Response $response, array $data, int $status = 200) {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public function setRoutes(){

        $this->app->post('/', [Observer::class, 'getWelcome']);
        $this->app->group('/eval', function (RouteCollectorProxy $group) {
            $group->post('', [Observer::class, 'getWelcome']);
            $group->post('/egulias', [Observer::class, 'checkEgulias']);
            $group->post('/format', [Observer::class, 'checkFormat']);
            $group->post('/domain', [Observer::class, 'checkEmailDomain']);
        });
        $this->app->group('/catch', function (RouteCollectorProxy $group) {
            $group->post('', [Catcher::class, 'getWelcome']);
        });
        $this->app->group('/mail', function (RouteCollectorProxy $group) {
            $group->post('', [Mailer::class, 'getWelcome']);
        });

        return 'Rutas exitosas!';
    }

    public function tosetDb(){
        //posible conexiona BD

        // $this->app->group('/eval', function (RouteCollectorProxy $group) {
        //     $group->post("/", function (Request $request, Response $response) {
        //         //Muestra de conexion a base de datos
        //         $bd = $this->container->get(\Resources\Database::class);
        //         $bd = new \Resources\Database();
        //         $data = $this->observer->getConnection($bd);
                
        //         $response->getBody()->write(json_encode($data));
        //         return $response->withHeader('Content-Type', 'application/json');
        //     });
        // });
    }
    
}