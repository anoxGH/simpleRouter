<?php
namespace AnoxGH\SimpleRouter;

/**
 * Class Router
 *
 * @author    Sebastian Gieselmann <s.gieselmann@live.com>
 * @copyright Copyright (c) 2015, Sebastian Gieselmann
 * @package   AnoxGH\Router
 * @link      https://github.com/anoxGH/simpleRouter
 * @license   http://opensource.org/licenses/GPL-2.0
 *
 *   @method static Router get(string $route, Callable $callback)
 *   @method static Router post(string $route, Callable $callback)
 *   @method static Router put(string $route, Callable $callback)
 *   @method static Router delete(string $route, Callable $callback)
 *   @method static Router options(string $route, Callable $callback)
 *   @method static Router head(string $route, Callable $callback)
 */
class Router
{



    /**
     * Registered routes
     * @var array
     */
    public static $routes = array();


    /**
     * Registered method
     * @var array
     */
    public static $methods = array();


    /**
     * Registered callbacks
     * @var array
     */
    public static $callbacks = array();


    /**
     * Registered Patterns
     * @var array
     */
    public static $patterns = array(
        ':any' => '[^/]',
        ':num' => '[0-9]+',
        ':all' => '.*'
    );


    /**
     * Error callback (404)
     * @var null
     */
    public static $errorCallback = NULL;



    /**
     * Register route entrypoint
     *
     * @param string $method
     * @param array  $params
     */
    public static function __callstatic($method, $params)
    {
        $uri      = $params[0];
        $callback = $params[1];

        array_push(self::$routes, $uri);
        array_push(self::$methods, strtoupper($method));
        array_push(self::$callbacks, $callback);
    }



    /**
     * Register error callback
     *
     * @param $callback
     */
    public static function error($callback)
    {
        self::$errorCallback = $callback;
    }



    /**
     * Dispatch Request
     */
    public static function dispatch()
    {
        $uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method   = $_SERVER['REQUEST_METHOD'];
        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        // check if route is defined without regex
        if (in_array($uri, self::$routes))
        {


            $routePos = array_keys(self::$routes, $uri);

            foreach ($routePos as $route)
            {
                if (self::$methods[$route] == $method)
                {
                    // call closure
                    call_user_func(self::$callbacks[$route]);
                    return;

                }
            }

        }
        else
        {
            // REGEX for lambda URI's
            $pos = 0;
            foreach (self::$routes as $route)
            {
                if (strpos($route, ':') !== FALSE)
                {
                    $route = str_replace($searches, $replaces, $route);
                }
                if (preg_match('#^' . $route . '$#', $uri, $matched))
                {
                    if (self::$methods[$pos] == $method)
                    {
                        array_shift($matched);

                        // call closure
                        call_user_func_array(self::$callbacks[$pos], $matched);
                        return;

                    }
                }
                $pos++;
            }
        }
        // No routes found?
        if (!self::$errorCallback)
        {
            self::$errorCallback = function ()
            {
                header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");
                echo '404';
            };
        }
        call_user_func(self::$errorCallback);
    }

}