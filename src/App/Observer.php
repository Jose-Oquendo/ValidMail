<?php
declare(strict_types=1);

namespace App;

class Observer
{
    private $conn;

    public function __construct(){
        $this->conn = null;
    }

    public function getWelcome(): array
    {
        $info = ["message" => "Hola Mundo!"];
        return $info;
    }

    public function getConnection($repo): array
    {
        $this->conn = $repo;
        $info = [
            [
                "id" => 1,
                "desciption" => "This is product one",
                "name" => 'Produt One'
            ],
            [
                "id" => 2,
                "desciption" => null,
                "name" => 'Produt Two'
            ]
        ];
        return $info;
    }
}