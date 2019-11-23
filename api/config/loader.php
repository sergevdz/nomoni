<?php

// Creates the autoloader
$loader = new \Phalcon\Loader();

// Register some directories
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->libDir,
        $config->application->middlewaresDir,
        $config->application->vendorDir,
    ]
);

// Register autoloader
$loader->register();