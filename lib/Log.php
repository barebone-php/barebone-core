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

use \Monolog\Logger as Monolog;
use \Monolog\Handler\StreamHandler;

/**
 * Log
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
class Log
{

    /**
     * Monolog Instance
     *
     * @var \Monolog\Logger
     */
    private static $_instance = null;

    /**
     * Instantiate Monolog
     *
     * @return Monolog
     */
    public static function instance()
    {
        if (null === self::$_instance) {
            $logger = new Monolog(Config::read('app.id'));
            $file_handler = new StreamHandler(self::getFilepath());
            $logger->pushHandler($file_handler);

            self::$_instance = $logger;
        }
        return self::$_instance;
    }

    /**
     * Return the log destination
     *
     * @return string Full path to log file
     */
    public static function getFilepath()
    {
        return PROJECT_ROOT . 'tmp' . DS . 'logs' . DS . 'app.log';
    }

    /**
     * Log "INFO" message
     *
     * @param string $message Message to write
     *
     * @return Boolean Whether the record has been processed
     */
    public static function info($message)
    {
        return self::instance()->addInfo($message);
    }

    /**
     * Log "WARNING" message
     *
     * @param string $message Message to write
     *
     * @return Boolean Whether the record has been processed
     */
    public static function warning($message)
    {
        return self::instance()->addWarning($message);
    }

    /**
     * Log "ERROR" message
     *
     * @param string $message Message to write
     *
     * @return Boolean Whether the record has been processed
     */
    public static function error($message)
    {
        return self::instance()->addError($message);
    }
}
