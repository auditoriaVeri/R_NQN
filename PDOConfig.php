<?php
require_once __DIR__ . '/utilidades.php';

cargarEnv();

class PDOConfig extends PDO {

    private $engine;
    private $host;
    private $database;
    private $user;
    private $pass;
    private $debug;

    public function __construct(){
        $this->engine = 'mysql';
        
        // Variables Fail-Fast
        $this->host = $_ENV['db_host'] ?? null;
        $this->database = $_ENV['db_taller'] ?? null;
        $this->user = $_ENV['db_user'] ?? null;
        $this->pass = $_ENV['vtousr_pass'] ?? null;

        if (!$this->host || !$this->database || !$this->user || !$this->pass) {
            die("ERROR CRITICO: Faltan variables obligatorias de BD en .env.master\n");
        }

        $this->debug = false;

        $dns = $this->engine.':dbname='.$this->database.";host=".$this->host;
        parent::__construct( $dns, $this->user, $this->pass,
        array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    }

    public function query($sql){
        if($this->debug){
            echo "Consulta a ejecutar: /** ".$sql.' **/ <br />';
        }

        $resultado = parent::query($sql);

        if(!$resultado){
            if($this->debug){
                print_r($this->errorInfo());
            }
            return false;
        }else
            return $resultado;
    }

    public function setDebug($debug){
        $this->debug = $debug;
    }

    public function getDebug(){
        return $this->debug;
    }

    /**
     * filtra un string de posibles ataques
     *
     * @param string $variable
     * @return string variable escapando posibles ataques
     */
    public function filtrar($variable) {
        if (get_magic_quotes_gpc()) {
            $variable = stripslashes($variable);
        }
        if (!is_numeric($variable)) {
            $variable = substr($this->quote($variable),1,-1);
        }
        return $variable;
    }
}
?>
