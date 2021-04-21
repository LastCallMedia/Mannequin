<?php


namespace LastCall\Mannequin\Core\Provider;


use Application\Application;
use LastCall\Mannequin\Core\ControllerCollection;

class ControllerProviderInterface
{
    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app);

}
