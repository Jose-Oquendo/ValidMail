<?php
declare(strict_types=1);

namespace App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Catcher
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

    public function emailVerification(Request $request, Response $response): Response
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);
            $email = $data['email'] ?? null;

            $client = new Client(['base_uri' => $_ENV['HUNTER_API']]);
            $apiResponse = $client->request('GET', '/v2/email-verifier', [
                'query' => [
                    'email' => $email,
                    'api_key' => $_ENV['HUNTER_KEY'],
                ]
            ]);
            $status = $apiResponse->getStatusCode();
            $body = json_decode(strval($apiResponse->getBody()), true);

            return $this->jsonResponse($response, [
                "valid" => $body['data']['status'],
                "data" => $body,
                "status" => $status
            ]);
        } catch (RequestException $e) {
            return $this->jsonResponse($response, [
                "message" => $e->getMessage(),
                "status" => 400
            ]);
        }
    }
}