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

use \Zend\Diactoros\Response as ResponseInterface;

/**
 * Response
 *
 * @category  Class
 * @package   Barbone_Core
 * @author    Kjell Bublitz <kjbbtz@gmail.com>
 * @copyright 2016 Barebone.PHP
 * @license   https://goo.gl/D3yaAZ MIT Licence
 * @version   Release: @package_version@
 * @link      https://github.com/barebone-php/barebone-core
 */
class Response extends ResponseInterface
{
    /**
     * Set headers and output body
     *
     * @param ResponseInterface $response A compatible ResponseInterface Instance
     *
     * @return void
     */
    public static function send(ResponseInterface $response)
    {
        header(
            sprintf(
                'HTTP/%s %s %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            )
        );

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        $body = $response->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        $contentLength = $response->getHeaderLine('Content-Length');
        if (!$contentLength) {
            $contentLength = $body->getSize();
        }

        if (isset($contentLength)) {
            $amountToRead = $contentLength;
            while ($amountToRead > 0 && !$body->eof()) {
                $chunk = $body->read(min(4096, $amountToRead));
                echo $chunk;
                $amountToRead -= strlen($chunk);
                if (connection_status() != CONNECTION_NORMAL) {
                    break;
                }
            }
        } else {
            while (!$body->eof()) {
                echo $body->read(4096);
                if (connection_status() != CONNECTION_NORMAL) {
                    break;
                }
            }
        }
    }
}
