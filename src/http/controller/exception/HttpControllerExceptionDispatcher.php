<?php
/**
 * This file is part of WirexMedia common library.
 *
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/soloproyectos/php.common-libs/blob/master/LICENSE BSD 2-Clause License
 * @link    https://github.com/soloproyectos/php.common-libs
 */
namespace wirexmedia\common\http\controller\exception;
use \Exception;
use wirexmedia\common\event\EventDispatcher;

/**
 * Class HttpControllerExceptionDispatcher.
 *
 * @package Http\Controller\Exception
 * @author  Gonzalo Chumillas <gchumillas@email.com>
 * @license https://github.com/soloproyectos/php.common-libs/blob/master/LICENSE BSD 2-Clause License
 * @link    https://github.com/soloproyectos/php.common-libs
 */
class HttpControllerExceptionDispatcher extends EventDispatcher
{
    /**
     * Triggers an event.
     *
     * @param string $classname Exception class name
     * @param mixed  $exception Exception instance
     *
     * @see    EventDispatcher::trigger()
     * @return EventDisaptcherInterface
     */
    public function trigger($classname, $exception = null)
    {
        $eventListeners = $this->getEventListeners();
        $found = false;
        $this->setStopped(false);

        foreach ($eventListeners as $eventListener) {
            $subclass = $eventListener->getType();

            if ($subclass == $classname
                || is_subclass_of($exception, $subclass)
            ) {
                $found = true;

                try {
                    $eventListener->exec($exception);
                } catch (Exception $e) {
                    $this->trigger(get_class($e), $e);
                    break;
                }
            }

            if ($this->isStopped()) {
                break;
            }
        }

        if (!$found) {
            throw $exception;
        }

        return $this;
    }
}
