<?php
/**
 * This file is part of WirexMedia common library.
 *
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/wirexmedia-php/http-controller/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/wirexmedia-php/http-controllers
 */
namespace wirexmedia\http;
use \Exception;
use wirexmedia\event\EventDispatcher;
use wirexmedia\event\EventListener;
use wirexmedia\http\controller\event\HttpControllerEventListener;
use wirexmedia\http\controller\exception\HttpControllerExceptionDispatcher;
use wirexmedia\http\controller\data\HttpControllerDataRequest;
use wirexmedia\http\controller\data\HttpControllerDataSession;
use wirexmedia\http\controller\data\HttpControllerDataCookies;

/**
 * Class Controller.
 *
 * @package Http\Controller
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/wirexmedia-php/http-controller/blob/master/LICENSE The MIT License (MIT)
 * @link    https://github.com/wirexmedia-php/http-controller
 */
class Controller extends EventDispatcher
{
    /**
     * Error dispatcher.
     * @var HttpControllerExceptionDispatcher
     */
    private $_errorDispatcher;

    /**
     * Request parameters.
     * @var HttpControllerDataRequest
     */
    public $request;

    /**
     * Session variables.
     * @var HttpControllerDataSession
     */
    public $session;

    /**
     * Cookies.
     * @var HttpControllerDataCookies
     */
    public $cookies;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_errorDispatcher = new HttpControllerExceptionDispatcher();
        $this->request = new HttpControllerDataRequest();
        $this->session = new HttpControllerDataSession();
        $this->cookies = new HttpControllerDataCookies();
    }

    /**
     * Handles errors.
     *
     * @param string   $className      Exception class name
     * @param Callable $listener       Listener function
     * @param boolean  $isHighPriority Is high priority? (default is false)
     *
     * @return Controller
     */
    public function onError($className, $listener, $isHighPriority = false)
    {
        $eventListener = new EventListener($className, $listener);
        $eventListener->setOneTime(true);
        $eventListener->setHighPriority($isHighPriority);
        $this->_errorDispatcher->addEventListener($eventListener);
        return $this;
    }

    /**
     * Stops error propagation.
     *
     * @return Controller
     */
    public function stopErrorPropagation()
    {
        $this->_errorDispatcher->stopPropagation();
    }

    /**
     * Handles HTTP requests.
     *
     * This method overwrites `parent::on`.
     *
     * @param string   $type           Request type (start, get, post or end)
     * @param Callable $listener       Listener function
     * @param boolean  $isHighPriority Is high priority (default is false)
     *
     * @return Controller
     */
    public function on($type, $listener, $isHighPriority = false)
    {
        $eventListener = new HttpControllerEventListener($type, $listener);
        $eventListener->setHighPriority($isHighPriority);
        $eventListener->setErrorDispatcher($this->_errorDispatcher);
        $this->addEventListener($eventListener);
        return $this;
    }

    /**
     * Handles HTTP requests.
     *
     * This method overwrites `parent::one`.
     *
     * @param string   $type           Request type (start, get, post or end)
     * @param Callable $listener       Listener function
     * @param boolean  $isHighPriority Process the event at first place
     *
     * @return Controller
     */
    public function one($type, $listener, $isHighPriority = false)
    {
        $eventListener = new HttpControllerEventListener($type, $listener);
        $eventListener->setOneTime(true);
        $eventListener->setHighPriority($isHighPriority);
        $eventListener->setErrorDispatcher($this->_errorDispatcher);
        $this->addEventListener($eventListener);
        return $this;
    }

    /**
     * Processes HTTP request.
     *
     * @return Controller
     */
    public function done()
    {
        $requestMethod = strtolower($_SERVER["REQUEST_METHOD"]);
        $this->trigger(array("start", $requestMethod, "end"));
        return $this;
    }
}
