<?php
require_once("config.php");
require_once("QueryBuilder.class.php");

class Database {
    private static $_instance;
    private $_mysqli; // MySQL link
    
    protected $query = "";
    protected $query_builder;
    
    protected $last_query = null;
    
    public function __construct($host = DB_HOST, $username = DB_USER, $password = DB_PASSWORD, $db = DB_NAME, $port = null) 
    {
        if ($port === null) {
            $port = ini_get('mysqli.default_port');
        }
        
        $this->_mysqli = new mysqli($host, $username, $password, $db, $port) or die("Failed to connect to database.");
        $this->_mysqli->set_charset("utf8");
        
        $this->query_builder = new QueryBuilder();
        
        self::$_instance = $this;
    }
    
    public function select($fields) {
        $this->query_builder->set_fields($fields);
        
        return $this;
    }
    
    public function from($table) {
        $this->query_builder->set_table($table);   
        
        return $this;
    }
    
    public function where($field, $operator, $value) {
        $this->query_builder->add_where(array(
            "field" => $field,
            "operator" => $operator,
            "value" => $value
        ));  
        
        return $this;
    }
    
    public function limit($limit) {
        $this->query_builder->set_limit($limit);
        
        return $this;
    }
    
    public function insert($table, $values = array())
    {
        if (!is_array($values)) {
            die("Insert failed. Was expecting an array of values, but recieved " . gettype($values) . " instead.");
        }
        
        if (count($values) === 0)
        {
            die("Insert failed. There were no values to insert.");
        }
        
        $this->query_builder->set_table($table);
    }
    
    public function get($fields = null) {
        if ($fields !== null) {
            $this->query_builder->set_fields($fields);
        }
        
        $query = $this->query_builder->build_select_query();
        
        $this->last_query = $query;
        
        return $query;
    }
}

$db = new Database();
echo $db->select("id, status_id, run_time")
->from("jobs")
->where("id", "LIKE", 1)
->where("status_id", "<>", "2")
->limit(3)
->get();

/*
Same as above (short syntax)
$db->from("jobs")
->where("id", "LIKE", 1)
->where("status_id", "<>", "2")
->limit(3)
->get("id, status_id, run_time");

Select all from a table (SELECT * FROM jobs)
$db->from("jobs")->get(); // Defaults to asterisk.
$db->from("jobs)->get("*"); // Equivalent to the above.
*/