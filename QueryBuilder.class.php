<?php 

class QueryBuilder {
    // ALL
    private $table = null;
    private $fields = array();
    
    // SELECT specific
    private $is_distinct = false;
    private $order_by = array();
    private $group_by = array();
    private $limit = 0;
    
    // SELECT / UPDATE
    private $where = array();
    
    // UPDATE / INSERT
    private $values = array();
    
    protected $query = "";
    
    public function __construct() {
        
    }
    
    public function set_distinct($is_distinct) {
        $this->is_distinct = $is_distinct;
    }
    
    public function set_fields($fields) {
        $result = preg_replace("/['`\"\s]/ms", "", $fields);
        $this->fields = explode(",", $result);
    }
    
    public function set_table($table) {
        $this->table = trim($table);
    }
    
    public function set_group_by($group_by) {
        $result = preg_replace("/['`\"\s]/ms", "", $group_by);
        $this->group_by = explode(",", $result);
    }
    
    public function add_where($where) {
        echo "operator: " . $where["operator"] . "<br>";
        
        $where["operator"] = trim(strtoupper($where["operator"]));
        
        if (preg_match("/^(<=?|>=?|!?=|<=?>|(NOT\s)?LIKE|IS(\sNOT)?(\sNULL)?)$/", $where["operator"], $matches) === 0) {
            die("Failed to add WHERE clause. Try using a valid comparison operator.");
        }
        
        if ($where["operator"] === "IS NULL" || $where["operator"] === "IS NOT NULL") {
            $where["value"] = "";
        }
        
        array_push($this->where, $where);
    }
    
    public function set_limit($limit) {
        if (!is_int($limit)) {
           die("Error setting limit. Must be an integer.");
        }
        
        $this->limit = (int)$limit;
    }
    
    public function set_values($arr) {
        $this->values = $arr;
    }
    
    public function build_insert_query() {
        if ($this->table === null) {
            die("Invalid INSERT query. You have not specified a table.");
        }
        
        $this->query = "INSERT INTO ";
        
        $this->query .= $this->table . " ";
        
        if (count($fields) > 0) {
            $this->query .= "(" . implode(", ", $this->fields) . ") ";
        }
        
        $this->query .= "VALUES (" . implode(", ", $this->values) . ") ";
        
        $this->query = trim($this->query) . ";";
        
        $this->reset();
        
        return $this->query;
    }
    
    public function build_select_query() {
        
        $parts = array(
            "SELECT"         => "SELECT %s ",
            "SELECT_DISTICT" => "SELECT DISTINCT %s",
            "FROM"           => "FROM %s ",
            "WHERE"          => "WHERE %s ",
            "GROUP_BY"       => "GROUP BY %s ",
            "ORDER_BY"       => "ORDER BY %s ",
            "LIMIT"          => "LIMIT %d "
        );
        
        // Make sure there are some fields to look for, if not get them all!
        if (count($this->fields) === 0) {
            array_push($this->fields, "*");
        }
        
        // Query is invalid without a table specified.
        if ($this->table === null) {
            die("Invalid SELECT query. You have not specified a table.");
        }
        
        $fields = "`" . implode("`, `", $this->fields) . "` ";
        
        // SELECT
        if ($this->is_distinct !== true) {
            $this->query = sprintf($parts["SELECT"], $fields); 
        } else {
            $this->query = sprintf($parts["SELECT_DISTINCT"], $fields); 
        }
        
        // FROM
        $this->query .= sprintf($parts["FROM"], "`" . $this->table . "`") . " ";
        
        // WHERE
        if (count($this->where) > 0) {
            $conditions = "";
            
            for ($i = 0; $i < count($this->where); $i++) {
                $where = $this->where[$i];
                $value = (gettype($where["value"]) === "string") ? "'" . $where["value"] . "'" : $where["value"];
                $standalone = false;
                
                if ($where["operator"] === "IS NULL" || $where["operator"] === "IS NOT NULL") {
                    $standalone = true;
                }
                
                $conditions .= ($i !== 0 ? "AND " : "") . "`" . $where["field"] . "` " . $where["operator"] . " " . (!$standalone ? $value . " " : "");
                
            }
            
            $this->query .= sprintf($parts["WHERE"], $conditions);
        }
        
        // GROUP BY
        if (count($this->group_by) > 0) {
            $grouping = "`" . implode("`, `", $this->group_by) . "` ";
            $this->query .= sprintf($parts["GROUP_BY"], $grouping);
        }
        
        // ORDER BY
        if (count($this->order_by) > 0) {
            $order = "`" . implode("`, `", $this->order_by) . "` ";
            $this->query .= sprintf($parts["ORDER_BY"], $order);
        }
        
        
        // LIMIT
        if ($this->limit > 0) {
            $this->query .= sprintf($parts["LIMIT"], $this->limit);
        }
        
        $this->query = trim($this->query) .";";
        
        $this->reset();
        
        return $this->query;
    }
    
    public function toString() {
        return $this->query;
    }
    
    // For unit tests only
    public function dump() {
        $out = new stdClass();
        $out->table = $this->table;
        $out->fields = $this->fields;
        $out->where = $this->where;
        $out->order_by = $this->order_by;
        $out->group_by = $this->group_by;
        $out->limit = $this->limit;
        $out->values = $this->values;
        $out->query = $this->query;
        return $out;
    }
    
    private function reset() {
        $this->is_distinct = false;
        $this->table = null;
        $this->fields = array();
        $this->where = array();
        $this->order_by = array();
        $this->group_by = array();
        $this->limit = 0;
        $this->values = array();
    }
}

?>
