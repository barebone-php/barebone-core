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
 * Controller
 *
 * @category  Class
 * @package   Barbone_Core
 * @author    Kjell Bublitz <kjbbtz@gmail.com>
 * @copyright 2016 Barebone.PHP
 * @license   https://goo.gl/D3yaAZ MIT Licence
 * @version   Release: @package_version@
 * @link      https://github.com/barebone-php/barebone-core
 * @since     Class available since Release 0.1.0
 * @property  \Aura\Session\Segment $session
 */
class Controller
{
    use LogTrait;

    /**
     * Request Interface
     *
     * @var Request
     */
    protected $request;

    /**
     * Response Interface
     *
     * @var Response
     */
    protected $response;

    /**
     * Configuration Interface
     *
     * @var Config
     */
    protected $config;

    /**
     * Request data parsed from body
     * $_POST
     *
     * @var array|null
     */
    protected $data = null;

    /**
     * Middleware callbacks
     *
     * @var array
     */
    private $_middlewareBefore = [];

    /**
     * Middleware callbacks
     *
     * @var array
     */
    private $_middlewareAfter = [];

    /**
     * Controller constructor.
     *
     * @param Request  $request  Compatible Request Interface
     * @param Response $response Compatible Response Interface
     */
    public function __construct(Request $request, Response $response)
    {
        $this->setRequest($request);
        $this->setResponse($response);
        $this->config = Config::instance();
    }

    /**
     * You can overwrite this function to run code before
     * any action is called.
     *
     * @return void
     */
    public function initController()
    {
        // optional
    }

    /**
     * Render a view template with optional data
     *
     * @param string       $template Relative Path to Template File
     * @param array|object $data     Associate Array or Object (key => value)
     * @param int          $status   HTTP status code (100-599, default 200)
     *
     * @return Response
     */
    protected function render($template, $data = [], $status = 200)
    {
        $rendered = View::render($template, $data);

        $this->response->getBody()->write($rendered);

        return $this->getResponse()
            ->withStatus($status)
            ->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * Render data variable as application/json string
     *
     * @param array|object $data   Associate Array or Object (key => value)
     * @param int          $status HTTP status code (100-599, default 200)
     * @param int          $flags  JSON Encoding Flags
     *
     * @return Response
     */
    protected function renderJSON($data = [], $status = 200, $flags = null)
    {
        $rendered = View::renderJSON($data, $flags);

        $this->response->getBody()->rewind();
        $this->response->getBody()->write($rendered);

        return $this->getResponse()
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
    }


    /**
     * Redirect to URL with optional status
     *
     * @param string $url    The redirect destination.
     * @param int    $status The redirect HTTP status code (between 100 and 599).
     *
     * @return Response
     */
    protected function redirect($url, $status = 302)
    {
        return $this->getResponse()
            ->withStatus($status)
            ->withHeader('Location', (string)$url);
    }

    /**
     * Router Error Action
     *
     * @param string $error   Router::ERR_*
     * @param mixed  $subject Optional value for the error template
     *
     * @return Response
     */
    public function routerError($error = null, $subject = null)
    {
        switch ($error) {
        case Router::ERR_NOT_FOUND:
            $status = 404;
            $template = 'router/error404';
            break;
        case Router::ERR_BAD_METHOD:
            $status = 405;
            $template = 'router/error405';
            break;
        case Router::ERR_MISSING_CONTROLLER:
        case Router::ERR_MISSING_ACTION:
        default:
            $status = 500;
            $template = 'router/error';
            break;
        }

        return $this->render($template, compact('error', 'subject'), $status);
    }

    /**
     * Catch certain undefined properties and deliver something useful.
     *
     * @param string $name Undefined Property Name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ($name === 'session') {
            // start or resume session on use
            return Session::instance();
        }

        return null;
    }


    /**
     * Add middleware to be executed before the action has been called.
     *
     * @param callable $middleware (req,res,next)
     *
     * @return Controller
     */
    public function addBefore(callable $middleware)
    {
        $this->_middlewareBefore[] = $middleware;
        return $this;
    }

    /**
     * Add middleware to be executed after the action has been called.
     *
     * @param callable $middleware (req,res,next)
     *
     * @return Controller
     */
    public function addAfter(callable $middleware)
    {
        $this->_middlewareAfter[] = $middleware;
        return $this;
    }

    /**
     * Get list of callbacks to be run before the action
     *
     * @return array
     */
    public function getMiddlewareBefore()
    {
        return $this->_middlewareBefore;
    }

    /**
     * Get list of callbacks to be run after the action
     *
     * @return array
     */
    public function getMiddlewareAfter()
    {
        return $this->_middlewareAfter;
    }

    /**
     * Get the current request object
     *
     * @return ServerRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the current response object
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Assign a request object
     *
     * @param Request $request FIG-7 Request Interface
     *
     * @return self
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        $parsedBody = $this->request->getParsedBody();

        if (!empty($parsedBody)) {
            if (is_array($parsedBody)) {
                $this->data = array_map('trim', (array)$parsedBody);
            } else {
                $this->data = $parsedBody;
            }
        }

        return $this;
    }

    /**
     * Assign a response objects
     *
     * @param Response $response FIG-7 Request Interface
     *
     * @return self
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }
}
