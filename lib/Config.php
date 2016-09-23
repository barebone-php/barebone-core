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

/**
 * Config
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
class Config extends \Noodlehaus\Config
{
    /**
     * Return Default Configuration
     * Used if key not found within loaded file.
     *
     * @return array
     */
    protected function getDefaults()
    {
        return [
            "app"   => [
                "id"          => "my-app",
                "name"        => "My Application",
                "environment" => "development",
                "debug"       => true
            ],
            "mysql" => [
                "host"      => "localhost",
                "database"  => "test",
                "username"  => "mysql_user",
                "password"  => "mysql_pass",
                "port"      => "3306",
                "charset"   => "utf8",
                "collation" => "utf8_unicode_ci"
            ]
        ];
    }

    /**
     * Configuration Interface
     *
     * @var Config
     */
    private static $_instance = null;

    /**
     * Instantiate Loader
     *
     * @return Config
     */
    public static function instance()
    {
        if (null === self::$_instance) {
            if (!defined('APP_ROOT')) {
                $path = __DIR__ . '/../../app/config.json';
            } else {
                $path = APP_ROOT . 'config.json';
            }

            if (!file_exists($path)) {
                $config = json_encode(self::$defaults, JSON_PRETTY_PRINT);
                file_put_contents($path, $config);
            }
            self::$_instance = new static($path);
        }
        return self::$_instance;
    }

    /**
     * Read configuration value
     *
     * @param string $key     Configuration-Key Path
     * @param mixed  $default Default value if key is empty/not-found
     *
     * @return mixed
     */
    public static function read($key = '', $default = null)
    {
        if (empty($key)) {
            return self::instance()->all();
        }
        if (!self::exists($key)) {
            return $default;
        }
        return self::instance()->get($key);
    }

    /**
     * Check if key path exists
     *
     * @param string $key Configuration-Key Path
     *
     * @return boolean
     */
    public static function exists($key = '')
    {
        return self::instance()->has($key);
    }
}
