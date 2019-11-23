<?php

defined('APP_PATH') || define('APP_PATH', realpath('.'));

use Phalcon\Config\Adapter\Ini as ConfigIni;

$cfg = new ConfigIni(__DIR__ . '/../config.ini');

return new \Phalcon\Config([
    'database' => [
        'adapter'    => $cfg->database->adapter,
        'host'       => $cfg->database->host,
        'username'   => $cfg->database->username,
        'password'   => $cfg->database->password,
        'dbname'     => $cfg->database->dbname,
        'options' => [
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_PERSISTENT => TRUE,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // FETCH_OBJ
        ]
    ],
    'application' => [
        'modelsDir'      => APP_PATH . '/app/models/',
        'libDir'      => APP_PATH . '/lib/',
        'vendorDir'      => APP_PATH . '/lib/vendor/',
        'controllersDir'      => APP_PATH . '/app/controllers/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'middlewaresDir' => APP_PATH . '/middlewares/',
        'baseUri'        => '/',
    ],
    'jwtkey' => $cfg->jwt->key,
]);
