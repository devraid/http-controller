<?php
/**
 * This file is part of WirexMedia common library.
 *
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/wirexmedia-php/http-controller/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/wirexmedia-php/http-controller
 */
namespace wirexmedia\http\controller\data;
use wirexmedia\arr\ArrHelper;
use wirexmedia\http\exception\HttpException;

/**
 * Class HttpControllerDataSession
 *
 * @package Event
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/wirexmedia-php/http-controller/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/wirexmedia-php/http-controller
 */
class HttpControllerDataSession
{
    /**
     * Gets a request attribute.
     *
     * @param string $name    Request attribute.
     * @param string $default Default value (not required)
     *
     * @return mixed
     */
    public function get($name, $default = "")
    {
        HttpControllerDataSession::start();
        return ArrHelper::get($_SESSION, $name, $default);
    }

    /**
     * Sets a request attribute.
     *
     * @param string $name  Request attribute.
     * @param mixed  $value Request value.
     *
     * @return void
     */
    public function set($name, $value)
    {
        HttpControllerDataSession::start();

        if (!preg_match("/^[\_a-z]/i", $name)) {
            throw new HttpException("Invalid session attribute: $name");
        }

        $_SESSION[$name] = $value;
    }

    /**
     * Does the request attribute exist?
     *
     * @param string $name Request attribute.
     *
     * @return boolean
     */
    public function is($name)
    {
        HttpControllerDataSession::start();
        return ArrHelper::is($_SESSION, $name);
    }

    /**
     * Deletes a request attribute.
     *
     * @param string $name Request attribute.
     *
     * @return void
     */
    public function del($name)
    {
        HttpControllerDataSession::start();
        ArrHelper::del($_SESSION, $name);
    }

    /**
     * Deletes all session variables.
     *
     * @return void
     */
    public static function clear()
    {
        HttpControllerDataSession::start();
        session_unset();
    }

    /**
     * Starts a session, if not already started.
     *
     * @return void
     */
    public static function start()
    {
        if (!session_id()) {
            session_start();
        }
    }

    /**
     * Saves data and closes the current session.
     *
     * @return void
     */
    public static function close()
    {
        HttpControllerDataSession::start();
        session_write_close();
    }
}
