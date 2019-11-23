<?php

class Message
{
	function __construct() {}

    /**
     * Create default success array message used in front-end
     *
     * @param string $content
     * @return array
     */
    public static function success (string $content) : array
    {
        return ['title' => 'Success!', 'content' => $content];
    }

    /**
     * Create default warning array message used in front-end
     *
     * @param string $content
     * @return array
     */
    public static function warning (string $content) : array
    {
        return ['title' => 'Warning!', 'content' => $content];
    }

    /**
     * Create default error array message used in front-end
     *
     * @param string $content
     * @return array
     */
    public static function error (string $content) : array
    {
        return ['title' => 'Error!', 'content' => $content];
    }

    /**
     * Create default exception array messages
     *
     * @param object $e
     * @return array
     */
    public static function exception (object $e) : array
    {
        return [
            'class' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ];
    }
}
