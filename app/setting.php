<?php

use Symfony\Component\Console\Application;

use Psr\Container\ContainerInterface;

return function (ContainerInterface $container) {
    $container->set('settings', function () {
        return [
            'displayErrorDetails' => true,
            'logErrorDetails' => true,
            'logErrors' => true
        ];
    });

};