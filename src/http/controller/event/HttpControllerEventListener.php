<?php
/**
 * This file is part of WirexMedia common library.
 *
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/wirexmedia-php/http-controller/blob/master/LICENSE BSD 2-Clause License
 * @link    https://github.com/wirexmedia-php/http-controller
 */
namespace wirexmedia\common\http\controller\event;
use \Exception;
use wirexmedia\common\event\EventListener;

/**
 * Class HttpControllerEventListener.
 *
 * @package Http\Controller\Event
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/wirexmedia-php/http-controller/blob/master/LICENSE BSD 2-Clause License
 * @link    https://github.com/wirexmedia-php/http-controller
 */
class HttpControllerEventListener extends EventListener
{
    /**
     * Error dispatcher.
     *
     * @var HttpControllerExceptionDispatcher
     */
    private $_errorDispatcher;

    /**
     * Gets the error dispatcher.
     *
     * @return HttpControllerExceptionDispatcher
     */
    public function getErrorDispatcher()
    {
        return $this->_errorDispatcher;
    }

    /**
     * Sets the error dispatcher.
     *
     * @param HttpControllerExceptionDispatcher $error Error dispatcher
     *
     * @return void
     */
    public function setErrorDispatcher($error)
    {
        $this->_errorDispatcher = $error;
    }

    /**
     * Calls the event listener.
     *
     * @param mixed $data Additional info (not required)
     *
     * @return HttpControllerEventHandler
     */
    public function exec($data = null)
    {
        try {
            call_user_func_array("parent::exec", func_get_args());
        } catch (Exception $e) {
            $this->_errorDispatcher->trigger(get_class($e), $e);
        }
    }
}
