<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=test;unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8',
    'enableSchemaCache' => true,

    // Duration of schema cache.
    'schemaCacheDuration' => 3600,

    // Name of the cache component used to store schema information
    'schemaCache' => 'cache',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',

];
