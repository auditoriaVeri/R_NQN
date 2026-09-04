<?php
class PDOConfig extends PDO {
  
    private $engine;
    private $host;
    private $database;
    private $user;
    private $pass;
  	private $debug;
    
    public function __construct(){
        $this->engine = 'mysql';
        $this->host = 'localhost';
        $this->database = 'TNQN_18_Control';
        $this->user = 'vtvusr';
        $this->pass = 'usuVTV25';
        $this->debug = false;
        
        $dns = $this->engine.':dbname='.$this->database.";host=".$this->host;
        parent::__construct( $dns, $this->user, $this->pass,
        array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    }
    
	    
	public function query($sql){
    if($this->debug){
        echo "Consulta a ejecutar: /** ".$sql.' **/ <br />';
    }    
    
    $resultado=parent::query($sql);
    
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
        // Este if se encargar� de retirar las barras en caso de que las comillas magicas estan habilitadas
        if (get_magic_quotes_gpc()) {
            $variable = stripslashes($variable);
        }
        if (!is_numeric($variable)) {
            $variable = substr($this->quote($variable),1,-1); 
        }
        return $variable;
    }
    
} 
