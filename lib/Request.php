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

use Zend\Diactoros\ServerRequestFactory as Factory;
use Zend\Diactoros\ServerRequest;

/**
 * Request
 *
 * @category  Class
 * @package   Barbone_Core
 * @author    Kjell Bublitz <kjbbtz@gmail.com>
 * @copyright 2016 Barebone.PHP
 * @license   https://goo.gl/D3yaAZ MIT Licence
 * @version   Release: @package_version@
 * @link      https://github.com/barebone-php/barebone-core
 * @since     Class available since Release 0.1.2
 */
class Request extends ServerRequest
{
    /**
     * Requested controller action name
     *
     * @var string
     */
    private $_action;

    /**
     * Create a request from the supplied superglobal values.
     *
     * If any argument is not supplied, the corresponding superglobal value will
     * be used.
     *
     * The ServerRequest created is then passed to the fromServer() method in
     * order to marshal the request URI and headers.
     *
     * @see ServerRequestFactory::fromServer()
     *
     * @throws InvalidArgumentException for invalid file values
     * @return Request
     */
    public static function createFromGlobals()
    {
        $server = Factory::normalizeServer($_SERVER);
        $files = Factory::normalizeFiles($_FILES);
        $headers = Factory::marshalHeaders($server);

        return new static(
            $server,
            $files,
            Factory::marshalUriFromServer($server, $headers),
            Factory::get('REQUEST_METHOD', $server, 'GET'),
            'php://input',
            $headers,
            $_COOKIE,
            $_GET,
            $_POST,
            static::_marshalProtocolVersion($server)
        );
    }

    /**
     * Return HTTP protocol version (X.Y)
     *
     * @param array $server The SERVER environment variable
     *
     * @return string
     */
    private static function _marshalProtocolVersion($server)
    {
        if (!isset($server['SERVER_PROTOCOL'])) {
            return '1.1';
        }

        if (!preg_match(
            '#^(HTTP/)?(?P<version>[1-9]\d*(?:\.\d)?)$#',
            $server['SERVER_PROTOCOL'], $matches
        )
        ) {
            throw new UnexpectedValueException(
                sprintf(
                    'Unrecognized protocol version (%s)',
                    $server['SERVER_PROTOCOL']
                )
            );
        }

        return $matches['version'];
    }

    /**
     * Return current controller action name
     *
     * @return string Name of a controller action (method)
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Set name of requested controller action
     *
     * @param string $action Name of a controller action (method)
     *
     * @return self
     */
    public function setAction($action)
    {
        $this->_action = $action;

        return $this;
    }

    /**
     * Get primary content-type header without encoding info
     *
     * @return string|null
     */
    public function getContentType()
    {
        $contentType = null;
        $_contentType = $this->getHeader('Content-Type');
        if (!empty($_contentType)) {
            $_contentType = explode(';', $_contentType[0]);
            $contentType = $_contentType[0];
        }

        return $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedBody()
    {
        $parsedBody = parent::getParsedBody();

        if (empty($parsedBody))  {
            if ($this->getContentType() == 'application/json')  {
                $input = file_get_contents("php://input");
                $parsedBody = json_decode($input, true);
            }
        }

        return $parsedBody;
    }
}
