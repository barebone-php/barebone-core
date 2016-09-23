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
 * LogTrait
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
trait LogTrait
{
    /**
     * Write to application log
     *
     * @param string $text     message
     * @param string $severity Either 'info', 'warn' or 'error'
     *
     * @return Boolean Whether the record has been processed
     */
    public function log($text, $severity = 'info')
    {
        if ($severity === 'warn' || $severity === 'warning') {
            return Log::warning($text);
        }

        if ($severity === 'err' || $severity === 'error') {
            return Log::error($text);
        }

        return Log::info($text);
    }
}
