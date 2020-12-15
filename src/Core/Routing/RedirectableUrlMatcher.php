<?php


namespace LastCall\Mannequin\Core\Routing;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Matcher\RedirectableUrlMatcher as BaseRedirectableUrlMatcher;


class RedirectableUrlMatcher extends BaseRedirectableUrlMatcher
{
    /**
     * {@inheritdoc}
     */
    public function redirect($path, $route, $scheme = null)
    {
        $url = $this->context->getBaseUrl().$path;
        $query = $this->context->getQueryString() ?: '';

        if ('' !== $query) {
            $url .= '?'.$query;
        }

        if ($this->context->getHost()) {
            if ($scheme) {
                $port = '';
                if ('http' === $scheme && 80 != $this->context->getHttpPort()) {
                    $port = ':'.$this->context->getHttpPort();
                } elseif ('https' === $scheme && 443 != $this->context->getHttpsPort()) {
                    $port = ':'.$this->context->getHttpsPort();
                }

                $url = $scheme.'://'.$this->context->getHost().$port.$url;
            }
        }

        return [
            '_controller' => function ($url) { return new RedirectResponse($url, 301); },
            '_route' => $route,
            'url' => $url,
        ];
    }

}
