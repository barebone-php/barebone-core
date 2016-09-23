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

use FastRoute\RouteCollector;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;

/**
 * Router
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
class Router
{
    const ERR_NOT_FOUND = 'route-not-found';
    const ERR_BAD_METHOD = 'method-not-allowed';
    const ERR_MISSING_CONTROLLER = 'missing-controller';
    const ERR_MISSING_ACTION = 'missing-action';

    /**
     * A container for a collection of routes
     *
     * @var RouteCollector
     */
    private static $_instance;

    /**
     * Instantiate Router or return $instance
     *
     * @return RouteCollector
     */
    public static function instance()
    {
        if (null === self::$_instance) {
            $routeCollector = new RouteCollector(
                new \FastRoute\RouteParser\Std,
                new \FastRoute\DataGenerator\GroupCountBased
            );
            self::$_instance = $routeCollector;
        }
        return self::$_instance;
    }

    /**
     * Start router and parse incoming requests
     *
     * @return \Zend\Diactoros\Response
     */
    public static function dispatch()
    {
        $vars = [];

        $request = Request::createFromGlobals();

        $dispatcher = new Dispatcher(static::instance()->getData());
        $routeInfo = $dispatcher->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath()
        );

        $controller = "\\Barebone\\Controller";
        $action = "index";

        switch ($routeInfo[0]) {
        case Dispatcher::NOT_FOUND:
            $action = 'routerError';
            $vars['error'] = static::ERR_NOT_FOUND;
            $vars['subject'] = $request->getUri()->getPath();
            break;

        case Dispatcher::METHOD_NOT_ALLOWED:
            $action = 'routerError';
            $vars['error'] = static::ERR_BAD_METHOD;
            $vars['subject'] = $routeInfo[1];
            break;

        case Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];

            if (!class_exists($handler[0])) {
                $action = 'routerError';
                $vars['error'] = static::ERR_MISSING_CONTROLLER;
                $vars['subject'] = $handler[0];
            } elseif (!method_exists($handler[0], $handler[1])) {
                $action = 'routerError';
                $vars['error'] = static::ERR_MISSING_ACTION;
                $vars['subject'] = $handler[0] . '::' . $handler[1];
            } else {
                $controller = $handler[0];
                $action = $handler[1];
            }
            break;
        }

        // Start with an empty response
        $response = new Response();
        $request->setAction($action);

        // @var Controller $instance
        $instance = new $controller($request, $response);
        $instance->initController();

        // Get Middleware stacks
        $before = $instance->getMiddlewareBefore();
        $after = $instance->getMiddlewareAfter();

        // Append the action call to the middleware stack
        $before[] = function (Request $request, Response $response,
            callable $next = null
        ) use ($instance, $action, $vars) {

            // assigned updated objects
            $instance->setRequest($request);
            $instance->setResponse($response);

            // get updated response from action
            $response = call_user_func_array([$instance, $action], $vars);

            // continue
            return $next($instance->getRequest(), $response);
        };

        // Run the middleware stack and return the Response
        $middlewares = array_reverse(array_merge($before, $after));
        $runner = \Sirius\Middleware\Runner::factory($middlewares);

        return $runner($request, $response);
    }

    /**
     * We assume to receive a string with namespace prefix (starting with backslash).
     *
     * If the initial backslash is missing, we prefix \App\Controller
     * namespace to allow for shorter controller callbacks.
     *
     * @param mixed $callback "MyController:method" or array(class,method)
     *
     * @throws \LogicException
     * @return array
     */
    protected static function callback($callback)
    {
        if (is_string($callback)) {
            // default format is "ControllerClassName:action"
            if (strpos($callback, ':')) {
                // automatically prefix namespace on callbacks not starting with '\'
                if ($callback{0} !== '\\') {
                    $callback = "\\App\\Controller\\{$callback}";
                }
                $callback = explode(':', $callback);
            } else {
                // default to "index" method in "whatever is callback class"
                $callback = [$callback, 'index'];
            }
        }
        if (!is_array($callback)) {
            throw new \LogicException(
                "A route callback could not be understood."
                ."Couldn't resolve to [class,action] array."
            );
        }
        return $callback;
    }

    /**
     * Handle GET HTTP requests
     *
     * @param string $path     URL segments and placeholders
     * @param mixed  $callback "MyController:method" or array(class,method)
     *
     * @return void
     */
    public static function get($path, $callback)
    {
        self::instance()->addRoute('GET', $path, self::callback($callback));
    }

    /**
     * Handle POST HTTP requests
     *
     * @param string $path     URL segments and placeholders
     * @param mixed  $callback "MyController:method" or array(class,method)
     *
     * @return void
     */
    public static function post($path, $callback)
    {
        self::instance()->addRoute('POST', $path, self::callback($callback));
    }

    /**
     * Handle PUT HTTP requests
     *
     * @param string $path     URL segments and placeholders
     * @param mixed  $callback "MyController:method" or array(class,method)
     *
     * @return void
     */
    public static function put($path, $callback)
    {
        self::instance()->addRoute('PUT', $path, self::callback($callback));
    }

    /**
     * Handle DELETE HTTP requests
     *
     * @param string $path     URL segments and placeholders
     * @param mixed  $callback "MyController:method" or array(class,method)
     *
     * @return void
     */
    public static function delete($path, $callback)
    {
        self::instance()->addRoute('DELETE', $path, self::callback($callback));
    }

    /**
     * Handle PATCH HTTP requests
     *
     * @param string $path     URL segments and placeholders
     * @param mixed  $callback "MyController:method" or array(class,method)
     *
     * @return void
     */
    public static function patch($path, $callback)
    {
        self::instance()->addRoute('PATCH', $path, self::callback($callback));
    }

    /**
     * Handle OPTIONS HTTP requests
     *
     * @param string $path     URL segments and placeholders
     * @param mixed  $callback "MyController:method" or array(class,method)
     *
     * @return void
     */
    public static function options($path, $callback)
    {
        self::instance()->addRoute('OPTIONS', $path, self::callback($callback));
    }

    /**
     * Route that handles all HTTP request methods
     *
     * @param array  $methods  List of HTTP methods to support, i.e: [GET,POST,EDIT]
     * @param string $path     URL segments and placeholders
     * @param mixed  $callback "MyController:method" or array(class,method)
     *
     * @return void
     */
    public static function map($methods, $path, $callback)
    {
        foreach ($methods as $httpMethod) {
            self::instance()->addRoute(
                $httpMethod, $path, self::callback($callback)
            );
        }
    }
}
