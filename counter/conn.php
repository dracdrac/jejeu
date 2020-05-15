<?php 
// echo "coucouuuu";
 // $config = include('/config/config.php');

### EDIT HERE ###

// DB CONNECT INFO
$db_host = $config['db']['host'];
$db_name = $config['db']['dbname'];
$db_user = $config['db']['username'];
$db_pw = $config['db']['password'];

// DB TABLE INFO
$GLOBALS['hits_table_name'] = "jejeu_hit_counter_hits";
$GLOBALS['info_table_name'] = "jejeu_hit_counter_info";

### STOP EDITING HERE ###

// CONNECT TO DB
try {   
	$GLOBALS['db'] = new PDO("mysql:host=".$db_host.";dbname=".$db_name, $db_user, $db_pw, array(PDO::ATTR_PERSISTENT => false, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false));  
}  
catch(PDOException $e) {  
    echo $e->getMessage();
}

?>