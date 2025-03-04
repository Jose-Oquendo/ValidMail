<?php
declare(strict_types=1);

use Slim\Exception\HttpInternalServerErrorException;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);

try {
  $dotenv->load();
  
  $api = AppFactory::create();
  
  $api->setBasePath("/services/ValMail/public");
  
  $api->addRoutingMiddleware();
  
  $repository = new \Routes\Router($api);
  $repository->setRoutes();
  
  $api->addErrorMiddleware(true, true, true);
  try {
    $api->run();
  } catch (HttpInternalServerErrorException $e) {    
    echo ( json_encode(array("status" => "failed", "message" => "An unexpected error occurred", "error" => $e)) ); 
  } catch (Exception $e) {    
    echo ( json_encode(array("status" => "failed", "message" => "This action is not allowed", "error" => $e)) ); 
  }

} catch (Exception $e) {
  echo json_encode(array("status" => "failed", "message" => "Failed to load .env file", "error" => $e->getMessage()));
  exit();
}
