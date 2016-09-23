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

use InvalidArgumentException;
use \Philo\Blade\Blade;

/**
 * View
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
class View
{
    /**
     * Blade instance
     * A simple and yet powerful Laravel Blade templating engine
     *
     * @var Blade;
     */
    private static $_instance = null;

    /**
     * Instantiate Blade Renderer
     *
     * @return Blade
     */
    public static function instance()
    {
        if (null === self::$_instance) {
            self::$_instance = new Blade(
                APP_ROOT . 'views',
                PROJECT_ROOT . 'tmp' . DS . 'cache'
            );
        }
        return self::$_instance;
    }

    /**
     * Render Blade template file with Data
     *
     * @param string $template Relative path to a ".blade.php" file
     * @param array  $data     Associative array of variables names and values.
     *
     * @return string HTML string
     */
    public static function render($template, $data = [])
    {
        // @var \Illuminate\View\Factory $factory
        $factory = self::instance()->view();

        // @var \Illuminate\Contracts\View\View $view
        $view = $factory->make($template, $data);

        return $view->render();
    }

    /**
     * Json Encode String with given flags
     *
     * @param array   $data  Associative array of variables names and values.
     * @param integer $flags json_encode options
     *
     * @throws InvalidArgumentException
     * @return string JSON string
     */
    public static function renderJSON($data = [], $flags = null)
    {
        if (is_null($flags)) {
            $flags = JSON_HEX_TAG | JSON_HEX_APOS
                | JSON_HEX_AMP | JSON_HEX_QUOT
                | JSON_UNESCAPED_SLASHES;
        }

        json_encode(null); // clear json_last_error()
        $result = json_encode($data, $flags);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unable to encode data to JSON in %s: %s', __CLASS__,
                    json_last_error_msg()
                )
            );
        }

        return $result;
    }
}
