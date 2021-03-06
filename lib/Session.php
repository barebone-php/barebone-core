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

use Aura\Session\Segment;
use Aura\Session\SessionFactory;

/**
 * Session
 *
 * @category  Class
 * @package   Barbone_Core
 * @author    Kjell Bublitz <kjbbtz@gmail.com>
 * @copyright 2016 Barebone.PHP
 * @license   https://goo.gl/D3yaAZ MIT Licence
 * @version   Release: @package_version@
 * @link      https://github.com/barebone-php/barebone-core
 * @since     Class available since Release 0.1.1
 */
class Session
{
    /**
     * SessionFactory Segment
     * This is a scoped variable container
     *
     * @var Segment
     */
    private static $_instance = null;

    /**
     * Instantiate Session Segment
     *
     * @return Segment
     */
    public static function instance()
    {
        if (null === self::$_instance) {
            $session_factory = new SessionFactory;
            $session = $session_factory->newInstance($_COOKIE);
            $segment = $session->getSegment('Barebone\Session');

            self::$_instance = $segment;
        }
        return self::$_instance;
    }


    /**
     * Returns the value of a key in the segment.
     *
     * @param string $key The key in the segment.
     * @param mixed  $alt An alternative value to return if the key is not set.
     *
     * @return mixed Value at key
     */
    public static function get($key, $alt = null)
    {
        return self::instance()->get($key, $alt);
    }

    /**
     * Sets the value of a key in the segment.
     *
     * @param string $key The key to set.
     * @param mixed  $val The value to set it to.
     *
     * @return void
     */
    public static function set($key, $val)
    {
        self::instance()->set($key, $val);
    }

    /**
     * Clear all data from the segment.
     *
     * @return null
     */
    public static function clear()
    {
        return self::instance()->clear();
    }

    /**
     * Sets a flash value for the *next* request.
     *
     * @param string $key The key for the flash value.
     * @param mixed  $val The flash value itself.
     *
     * @return void
     */
    public static function setFlash($key, $val)
    {
        self::instance()->setFlash($key, $val);
    }

    /**
     * Gets the flash value for a key in the *current* request.
     *
     * @param string $key The key for the flash value.
     * @param mixed  $alt An alternative value to return if the key is not set.
     *
     * @return mixed The flash value itself.
     */
    public static function getFlash($key, $alt = null)
    {
        return self::instance()->getFlash($key, $alt);
    }

    /**
     * Clears flash values for *only* the next request.
     *
     * @return null
     */
    public static function clearFlash()
    {
        return self::instance()->clearFlash();
    }

    /**
     * Gets the flash value for a key in the *next* request.
     *
     * @param string $key The key for the flash value.
     * @param mixed  $alt An alternative value to return if the key is not set.
     *
     * @return mixed The flash value itself.
     */
    public static function getFlashNext($key, $alt = null)
    {
        return self::instance()->getFlashNext($key, $alt);
    }

    /**
     * Sets a flash value for the *next* request *and* the current one.
     *
     * @param string $key The key for the flash value.
     * @param mixed  $val The flash value itself.
     *
     * @return void
     */
    public static function setFlashNow($key, $val)
    {
        return self::instance()->setFlashNow($key, $val);
    }

    /**
     * Clears flash values for *both* the next request *and* the current one.
     *
     * @return null
     */
    public static function clearFlashNow()
    {
        return self::instance()->clearFlashNow();
    }

    /**
     * Retains all the current flash values for the next request; values that
     * already exist for the next request take precedence.
     *
     * @return null
     */
    public static function keepFlash()
    {
        return self::instance()->keepFlash();
    }
}
