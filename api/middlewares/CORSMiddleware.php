<?php

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * CORSMiddleware
 *
 * CORS checking
 */
class CORSMiddleware implements MiddlewareInterface
{    

    /**
     * Before anything happens
     *
     * @param Event $event
     * @param Micro $application
     *
     * @return bool
     */
    public function beforeHandleRoute(Event $event, Micro $application) {
        $application
            ->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization')
            ->setHeader('Access-Control-Allow-Credentials', 'true');
            
            if ($application->request->isOptions()) {
                return true;
            }

            if ($application->request->getURI() === '/auth/login') {
                return true;
            }

            if ($application->request->getURI() === '/auth/signup') {
                return true;
            }
            
            $isValid = Auth::validateRequest($application->request, $application->config->jwtkey);

            if (!$isValid) {
                $application->response->setStatusCode(401, 'Unauthorized')
                    ->setJsonContent('Access is not authorized.')
                    ->send();
                return false;
            }

        return true;
    }

    /**
     * Calls the middleware
     *
     * @param Micro $application
     *
     * @return bool
     */
    public function call(Micro $application) {
        return true;
    }
}