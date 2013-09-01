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
```

#### Order of function calls doesn't matter!
All that really matters is that you call an execute function last!
``` php
<?php
    // Even if it really doesn't make sense, you're allowed to call these functions in any order as long as the execute statement is last.
    // Produces SELECT `favorite_color`, `age`, `first_name`, `id` FROM `users` WHERE `age` < 25 AND `favorite_color` = 'red' ORDER BY `id` DESC LIMIT 30;
    $db->order_by("id", "DESC")
    ->where("age", "<", 25)
    ->limit(30)
    ->where("favorite_color", "=", "red")
    ->from("users")
    ->select("favorite_color", "age", "first_name", "id")
    ->get(); // Execute statement (Must come last)
?>
```
    
