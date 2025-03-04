<?php
declare(strict_types=1);

namespace App;

use Slim\App;
use Resources\Database;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use LDAP\Result;

class Observer
{
    private $conn;

    public function __construct(){
        $this->conn = null;
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

    public function validateName($email){
        $NoPermitido = ['notiene', 'sincorreo', '@correo.com', '@NOTIENE', 'NOTIENE', 'NOTIENECORREO', 'SINCORREO', '@NOTIENECORREO', '@no.com', 'NOTENGOCORREO', 'notengocorreo',  ];

        foreach ($NoPermitido as $palabra) {
            if (strpos($email, $palabra) !== false) {
                return false; 
            }
        }
        return true;
    }

    public function validateEmail($email) {
        $validator = new EmailValidator();
        $multipleValidations = new MultipleValidationWithAnd([
            new DNSCheckValidation(),
            new RFCValidation()
        ]);
        if($validator->isValid($email, $multipleValidations)){
            return true;
        } else {
            return false;
        }
    }
    public function validateFormat($email) {
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            return true;
        }
    }
    public function validateDomain($email) {
        $com = $this->validateFormat($email);
        if($com){
            $domain = substr(strrchr($email, "@"), 1);
            if(checkdnsrr($domain, "MX")){
                return true;
            } else {
                return false;
            }
        } else {
            return $com;
        }
    }

    public function checkEgulias(Request $request, Response $response): Response
    {
        //Comprobar formato del correo y corregirlo
        $data = json_decode($request->getBody()->getContents(), true);
        $email = $data['email'] ?? null;
        if($this->validateName($email)){
            $validator = new EmailValidator();
            $multipleValidations = new MultipleValidationWithAnd([
                new DNSCheckValidation(),
                new RFCValidation()
            ]);
            if($validator->isValid($email, $multipleValidations)){
                return $this->jsonResponse($response, [
                    "resulta" => 'Egulias Valid email.', 
                    "status" => 200
                ]); 
            } else {
                return $this->jsonResponse($response, [
                    "resulta" => 'Egulias Invalid email format.', 
                    "status" => 400
                ]); 
            }
        } else {
            return $this->jsonResponse($response, [
                "resulta" => 'Email user or domain is no correct.', 
                "status" => 400
            ]);
        }
    }

    public function checkFormat(Request $request, Response $response): Response
    {
         //Comprobar formato del correo y corregirlo
         $data = json_decode($request->getBody()->getContents(), true);
         $email = $data['email'] ?? null;
 
         if($this->validateName($email)){
            $com = $this->validateFormat($email);
            if ($com) {
                return $this->jsonResponse($response, [
                    "resulta" => 'Valid email.', 
                    "status" => 200
                ]);
            } else {
                return $this->jsonResponse($response, [
                    "resulta" => 'Invalid email format.', 
                    "status" => 400
                ]);
            }
        } else {
            return $this->jsonResponse($response, [
                "resulta" => 'Email user or domain is no correct.', 
                "status" => 400
            ]);
        }
    }

    function checkEmailDomain(Request $request, Response $response): Response
    {
        //validar dominio y registros MX
        $data = json_decode($request->getBody()->getContents(), true);
        $email = $data['email'] ?? null;

        if($this->validateName($email)){
            $com = $this->validateFormat($email);
            if ($com) {
                $domain = substr(strrchr($email, "@"), 1);
                if(checkdnsrr($domain, "MX")){
                    return $this->jsonResponse($response, [
                        "resulta" => 'Email domain is accepted.', 
                        "status" => 200
                    ]);
                } else {
                    return $this->jsonResponse($response, [
                        "resulta" => 'Email domain is not reconized..', 
                        "status" => 400
                    ]);        
                }
            } else {
                return $this->jsonResponse($response, [
                    "resulta" => 'Invalid email format.', 
                    "status" => 400
                ]);
            } 
        } else {
            return $this->jsonResponse($response, [
                "resulta" => 'Email user or domain is no correct.', 
                "status" => 400
            ]);
        }
    }
}