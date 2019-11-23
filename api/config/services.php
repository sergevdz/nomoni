<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View\Simple as View;
use Phalcon\Mvc\Url as UrlResolver;

use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Model\Manager as ModelsManager;

use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

$di = new FactoryDefault();

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include __DIR__ . "/../config/config.php";
});

/**
 * Sets the view component
 */
$di->setShared('view', function () use ($config) {
    $view = new View();
    $view->setViewsDir($config->application->viewsDir);
    return $view;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () use ($config) {
    $dbConfig = $config->database->toArray();
    $adapter = $dbConfig['adapter'];
    unset($dbConfig['adapter']);

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;

    return new $class($dbConfig);
});

/**
 * Control columns
 */
$di->setShared('modelsManager', function () use ($config) {
    $eventsManager = new EventsManager();
    
    $validUser = Auth::getUserData($config);
    
    $eventsManager->attach(            
        'model:beforeValidationOnCreate',
        function (Event $event, $model) use ($validUser) {

            if ($validUser !== null) {
                $model->created_by = $validUser->id;
            }
            return true;
        }
    );

    $eventsManager->attach(
        'model:beforeUpdate',
        function (Event $event, $model) use ($validUser) {

            if ($validUser !== null) {
                $model->modified_by = $validUser->id;
                $model->modified = date('Y-m-d H:i:s');
            }
            return true;
        }
    );

    // Setting a default EventsManager
    $modelsManager = new ModelsManager();

    $modelsManager->setEventsManager($eventsManager);

    return $modelsManager;
});

/**
 * DB transactions service
 */
$di->setShared('transactions', function () {
    return new TransactionManager();
});
