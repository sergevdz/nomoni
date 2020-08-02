<?php
/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */

$app->options('/{catch:(.*)}', function() use ($app) {
    $app->response
        ->setStatusCode(200, 'OK');
        // ->setHeader('Access-Control-Allow-Origin', '*')
        // ->setHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
        // ->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization')
        // ->setHeader('Access-Control-Allow-Credentials', true)
        // ->sendHeaders()
        // ->send();
});

$app->get('/', function () use ($app) {
    $app->response
        ->setStatusCode(200, 'OK')
        ->setJsonContent(['result' => true, 'message' => 'Se ha desactivado la seguridad por tokens!!!.']);
});

include_once APP_PATH . '/routes.php';

/**
 * Not found handler
 */
$app->notFound(
    function () use ($app) {
        $app->response
            ->setStatusCode(404, 'Not Found')
            ->setJsonContent(['result' => false, 'message' => 'Not Found.']);
    }
);

/**
 * Error handler
 */
$app->error(
    function ($exception) use ($app) {
        // $app->response
        //     ->setStatusCode(500, 'Internal Error')
        //     ->setJsonContent(['result' => false, 'message' => 'Internal Error.']);
        throw $exception;
    }
);
