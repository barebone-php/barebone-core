<?php
/*
|--------------------------------------------------------------------------
| Definitions
|--------------------------------------------------------------------------
*/
define('DS', DIRECTORY_SEPARATOR);

if (!defined('PROJECT_ROOT')) {
	define('PROJECT_ROOT', dirname(__DIR__) . DS);
}

define('APP_ROOT', PROJECT_ROOT . 'app' . DS);
define('CORE_ROOT', PROJECT_ROOT . 'core' . DS);

/*
|--------------------------------------------------------------------------
| Require Composer Autoloader
|--------------------------------------------------------------------------
*/
if (file_exists(__DIR__ . DS . 'vendor' . DS . 'autoload.php')) {
    // core development (to be removed)
    require_once __DIR__ . DS . 'vendor' . DS . 'autoload.php';
}
require_once PROJECT_ROOT . 'vendor' . DS . 'autoload.php';

/*
|--------------------------------------------------------------------------
| Setup Database Connection
|--------------------------------------------------------------------------
*/
\Barebone\Database::boot();

/*
|--------------------------------------------------------------------------
| Load Application Routes
|--------------------------------------------------------------------------
*/
require_once(APP_ROOT . 'routes.php');