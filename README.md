## KoilDB: A MySQLi database class
--------------------------------------
### Get Started
All you need to do is instantiate it once:
``` php
<?php
    $db = new Database('host', 'user', 'password', 'database', 'port(optional)');
?>
```


### Syntax

#### Basic SELECT Queries
``` php
<?php
    // SELECT * FROM `some_table`;
    $db->from('some_table')->get(); 
    
    // The following produce an equivalent result;
    
    // SELECT `first_name`, `last_name`, `age` FROM `clients`;
    $db->select("first_name, last_name, age")
    ->from("clients")
    ->get(); 
    
    // SELECT `first_name`, `last_name`, `age` FROM `clients`;
    $db->from("clients")
    ->get("first_name, last_name, age"); 
?>
```

#### SELECT Queries with WHERE, ORDER BY, GROUP BY, and LIMIT
``` php
<?php
    // SELECT `first_name`, `last_name`, `age` FROM `clients` WHERE `age` > 50 AND `first_name` NOT LIKE 'Mary%` ORDER BY `last_name` DESC LIMIT 5;
    $db->select("first_name, last_name, age")
    ->from('clients')
    ->where("age", ">", 50)
    ->where("first_name", "NOT LIKE", "Mary%")
    ->order_by("last_name", "DESC")
    ->limit(5); 
?>
