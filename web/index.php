<?php

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 1);
date_default_timezone_set('CET');

// define el directorio web
define('WEB_ROOT', substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], '/index.php')));
// define el directorio de todos los archivos
define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
// define el directorio de los archivos base
define('CMS_PATH', ROOT_PATH . '/lib/base/');
// define el directorio de configuracion
define('CONFIG_PATH', ROOT_PATH . '/config/');
// define el directorio de controladores
define('CONTROLLER_PATH', ROOT_PATH.'/app/controllers/');
// define el directorio de modelos
define('MODEL_PATH', ROOT_PATH.'/app/models/');
// define el directorio de vistas
define('VIEW_PATH', ROOT_PATH.'/app/views/');

// starts the session
session_start();

// includes the system routes. Define your own routes in this file
include(CONFIG_PATH . 'routes.php');
require_once(ROOT_PATH.'/vendor/autoload.php');

/**
 * Standard framework autoloader
 * @param string $className
 */
function autoloader($className) {
	// controller autoloading
	if (strlen($className) > 10 && substr($className, -10) == 'Controller') {
		if (file_exists(CONTROLLER_PATH . $className . '.php') == 1) {
			require_once CONTROLLER_PATH . $className . '.php';
		}
	}
	else {
		if (file_exists(CMS_PATH . $className . '.php')) {
			require_once CMS_PATH . $className . '.php';
		}
		else if (file_exists(ROOT_PATH . '/lib/' . $className . '.php')) {
			require_once ROOT_PATH . '/lib/' . $className . '.php';
		}
		else {
			require_once MODEL_PATH .$className.'.php';
		}
	}
}

// activates the autoloader
spl_autoload_register('autoloader');

$router = new Router();
$router->execute($routes);