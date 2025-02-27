<?php
declare(strict_types=1);

namespace Resources;

use App\Observer;
use PDO;
use PDOException;
use Exception;

class Database {

    private $db;
    private $conn;
    private $controller_db = 'pgsql';
    private $host_db       = 'localhost';
    private $port_db       = 80; //change to the correct port
    private $user_db       = '';
    private $pass_db       = '';

    public function __construct($dbname){
        $this->db = $dbname;
        // $this->conn();
    }

    private function conn(){
        try {
            $this->conn = new PDO(
                "$this->controller_db:host=$this->host_db;dbname=$this->db;port=$this->port_db", "$this->user_db", "$this->pass_db"
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function begin(){
        try{
            $query = $this->conn->query("BEGIN");
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function sql($query, $data = array()){
        try{
            $sql = $this->conn->prepare($query);
            $res = $sql->execute($data);
            if(substr($query, 0, 6) == 'SELECT' || substr($query, 0, 6) == 'select' || stripos($query, 'returning') !== false){
                $res = $sql->fetchAll(PDO::FETCH_ASSOC);
                return $res;
            }
            return $sql->rowCount();
        }catch(PDOException $e){
            return $e -> getMessage();
        }
    }

    public function commit(){
        try{
            $query = $this->conn->query("COMMIT");
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function rollback(){
        try{
            $query = $this->conn->query("ROLLBACK");
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function close(){
        $this->conn = null;
    }
}