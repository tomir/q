<?php

session_start();

if (empty($_ENV['SLIM_MODE'])) {
    $_ENV['SLIM_MODE'] = (getenv('SLIM_MODE'))
        ? getenv('SLIM_MODE') : 'development';
}

$config = array();
 
$configFile = __DIR__ . '/../share/config/'
    . $_ENV['SLIM_MODE'] . '.php';
 
if (is_readable($configFile)) {
    require_once $configFile;
} else {
    require_once __DIR__ . '/../share/config/default.php';
}
 
// Routing
$config = [
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => '',
            'username' => '',
            'password' => '',
            'charset' => '',
            'collation' => '',
            'prefix' => ''
        ]
    ],

];
$app = new Slim\App($config);

$container = $app->getContainer();

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function($container) use ($capsule) {
    return $capsule;
};

$container['view'] = function ($container) {
    $view =  new \Slim\Views\Twig(__DIR__ . '/../templates/', [
        'cache' => false
    ]);


    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));


    return $view;
};

$app->group('/engine', function () {

    require __DIR__ . '/../app/routes.php';

});

$app->run();
