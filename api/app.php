<?php
/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */

$app->options('/{catch:(.*)}', function() use ($app) {
    $app->response
        ->setStatusCode(200, 'OK')
        ->send();
});

$app->get('/', function () use ($app) {
    $app->response
        ->setStatusCode(200, 'OK')
        ->setJsonContent([
            'result' => true,
            'message' => 'Se ha desactivado la seguridad por tokens!!!.'
        ])
        ->send();
});

include_once APP_PATH . '/routes.php';

/**
 * Not found handler
 */
$app->notFound(
    function () use ($app) {
        $app->response
            ->setStatusCode(404, 'Not Found')
            ->setJsonContent([
                'result' => false,
                'message' => 'Not Found.'
            ])
            ->send();
    }
);

/**
 * Error handler
 */
$app->error(
    function ($exception) use ($app) {
        throw $exception;
    }
);
