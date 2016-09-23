<?php
/**
 * Barebone Framework
 *
 * PHP Version 5
 *
 * @category  Barebone
 * @package   Barbone_Core
 * @author    Kjell Bublitz <kjbbtz@gmail.com>
 * @copyright 2016 Barebone.PHP
 * @license   https://goo.gl/D3yaAZ MIT Licence
 * @link      https://github.com/barebone-php/barebone-core
 */
namespace Barebone;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

/**
 * Database
 *
 * @category  Class
 * @package   Barbone_Core
 * @author    Kjell Bublitz <kjbbtz@gmail.com>
 * @copyright 2016 Barebone.PHP
 * @license   https://goo.gl/D3yaAZ MIT Licence
 * @version   Release: @package_version@
 * @link      https://github.com/barebone-php/barebone-core
 * @since     Class available since Release 0.1.0
 */
class Database
{

    /**
     * Capsule Instance
     * Manages the connection
     *
     * @param Capsule
     */
    private static $_instance;

    /**
     * Instantiate Eloquent ORM
     *
     * @return Capsule
     */
    public static function instance()
    {
        if (null === self::$_instance) {
            $capsule = new Capsule;
            $capsule->addConnection(self::getConfig());
            $capsule->setEventDispatcher(new Dispatcher(new Container));
            $capsule->setAsGlobal();

            self::$_instance = $capsule;
        }
        return self::$_instance;
    }

    /**
     * Return connection config
     *
     * @return array
     */
    public static function getConfig()
    {
        return array_merge(
            [
                'driver'    => 'mysql',
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
            ], Config::read('mysql', $default = [])
        );
    }

    /**
     * Boot Eloquent
     *
     * @return void
     */
    public static function boot()
    {
        self::instance()->bootEloquent();
    }
}
