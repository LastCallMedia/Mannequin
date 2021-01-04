<?php


namespace LastCall\Mannequin\Core\Routing;


use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Security trait.
 */
trait SecurityTrait
{
    public function secure($roles)
    {
        $this->before(function ($request, $app) use ($roles) {
            if (!$app['security.authorization_checker']->isGranted($roles)) {
                throw new AccessDeniedException();
            }
        });
    }
}
