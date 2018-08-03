<?php
$container = $app->getContainer();

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname']
        . ';charset=' . $db['charset'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $pdo;
};

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

$container['view'] = function ($c): \Slim\Views\Twig {
    $pathRoot = realpath(__DIR__ . "/../../");

    //require_once '../vendor/fzaninotto/faker/src/autoload.php';
    // create caching directory
    if (is_dir($pathRoot . "/cache") === false) {
        @mkdir($pathRoot . "/cache");
    }

    $view = new \Slim\Views\Twig(__DIR__ . "/../lib/View/", [
        'cache' => false, #$pathRoot . "/cache",
        "debug" => true
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));

    // This line should allow the use of {{ dump() }}
    $view->addExtension(new Twig_Extension_Debug());

    //$countries = new \Slim\Helper\Countries($this->db);

    // custom var
    $view->offsetSet("SITE_TITLE", SITE_TITLE); // twig: {{ SITE_TITLE }}
    $view->offsetSet("SITE_URL", SITE_URL); // twig: {{ SITE_URL }}
    $view->offsetSet("YEAR", YEAR); // twig: {{ YEAR }}

    return $view;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};
