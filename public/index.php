<?php
require_once '../vendor/Slim/Slim.php';
require_once '../vendor/RedBean/rb.php';

use Slim\Slim;
use Slim\Views;
use Slim\Middleware\CsrfGuard;

// Register autoloaders
Slim::registerAutoloader();
require_once '../app/autoloader.php';

// The session lines must come after the requires to allow (de)serialize to work.
session_cache_limiter(false);
session_start();

$mode = 'DEBUG'; // Change before deploying

// Setup the Slim app instance.
$app = new Slim(array(
    'debug' => $mode == 'DEBUG',
    'view' => new Views\Twig(),
    'templates.path' => '../app/Views',
    'cookies.encrypt' => true,
    'cookies.httponly' => true,
    'cookies.lifetime' => '2 weeks',
    'cookies.secret_key' => 'InsertSecretKeyHere'
));
$app->add(new CsrfGuard());

// Setup the Twig view within the app.
$view = $app->view();
$view->parserOptions = array(
    'debug' => $mode == 'DEBUG',
    'cache' => __DIR__ . '/../cache'
);
$view->parserExtensions = array(
    new Views\TwigExtension()
);

$basePath = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
$cssPath = $basePath . '/css/';
$jsPath = $basePath . '/js/';

$twig = $view->getEnvironment();
$twig->addGlobal('app_name', 'nohassl');
$twig->addGlobal('css_dir', $cssPath);
$twig->addGlobal('js_dir', $jsPath);
$twig->addGlobal('session', $_SESSION);

// Setup the RedBean ORM instance.

R::setup('mysql:host=127.0.0.1:3307;dbname=nohassl', 'root', '');

define('REDBEAN_MODEL_PREFIX', '\\Models\\');
// R::nuke(); // Uncomment to reset the database.
//R::setup();
//R::freeze( TRUE );

// Create route controllers
new Controllers\CustomErrors();
new Controllers\Root();
new Controllers\Login();
new Controllers\Register();
new Controllers\Profile();

// Run the app and close the ORM connection.
$app->run();
R::close();
