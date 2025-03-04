<?php
declare(strict_types=1);

namespace App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Mailer
{
    public function __construct(){
    }

    public function jsonResponse(Response $response, array $data, int $status = 200) {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public function getWelcome(Request $request, Response $response): Response
    {
        return $this->jsonResponse($response, [
            "message" => "Bienvenido a ValMail! Haga uso de nuestros endpoints para la validacion y manejo de correos electronicos :D"
        ]);
    }
}