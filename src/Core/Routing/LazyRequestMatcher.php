<?php


namespace LastCall\Mannequin\Core\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class LazyRequestMatcher implements RequestMatcherInterface
{

    private $factory;

    public function __construct(\Closure $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Returns the corresponding RequestMatcherInterface instance.
     *
     * @return UrlMatcherInterface
     */
    public function getRequestMatcher()
    {
        $matcher = call_user_func($this->factory);
        if (!$matcher instanceof RequestMatcherInterface) {
            throw new \LogicException("Factory supplied to LazyRequestMatcher must return implementation of Symfony\Component\Routing\RequestMatcherInterface.");
        }

        return $matcher;
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        return $this->getRequestMatcher()->matchRequest($request);
    }
}
