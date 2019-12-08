<?php

date_default_timezone_set('America/Mexico_City');
error_reporting(E_ALL);

use Phalcon\Mvc\Micro;
use Phalcon\Events\Manager;


define('APP_PATH', realpath('..'));

try {

    /**
     * Include composer autoloader
     */
    require APP_PATH . '/vendor/autoload.php';

    /**
     * Read the configuration
     */
    $config = include APP_PATH . '/config/config.php';

    /**
     * Include Autoloader.
     */
    include APP_PATH . '/config/loader.php';
    
    /**
     * Include Services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Call the autoloader service.  We don't need to keep the results.
     */
    // $di->getLoader();

    /*
     * Create a events manager
     */
    $eventsManager = new Manager();

    /**
     * Starting the application
     * Assign service locator to the application
     */
    $app = new Micro($di);
    
    $eventsManager->attach('micro', new CORSMiddleware());
    $app->before(new CORSMiddleware());
    
    $eventsManager->attach('micro', new ResponseMiddleware());
    $app->before(new ResponseMiddleware());

    $app->setEventsManager($eventsManager);

    /**
     * Include Application
     */
    include APP_PATH . '/app.php';

    /**
     * Handle the request
     */
    $app->handle();

} catch (Exception $e) {
    $response = new Phalcon\Http\Response();
    $response->setStatusCode(500, 'Internal Server Error');
    $response->setJsonContent(['result' => 'error', 'message' => $e->getMessage(), 'details' => $e->getTraceAsString()]);
    $response->send();
} 
