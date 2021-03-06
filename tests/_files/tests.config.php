<?php
return array(
    
    'db' => array(
        'dataset' => TESTS_ROOT . '_files/dataset.xml'
    ), 
    
    'mysql_lite' => array(
        'session_table' => 'session', 
        'authorization_code_table' => 'authorization_code', 
        'access_token_table' => 'access_token', 
        'refresh_token_table' => 'refresh_token', 
        
        'adapter' => array(
            'driver' => 'Pdo_Mysql', 
            'host' => 'localhost', 
            'username' => 'phpid_admin', 
            'password' => 'Phpid_admiN', 
            'database' => 'phpidserver_test'
        )
    )
);

