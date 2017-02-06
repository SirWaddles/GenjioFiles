<?php

namespace GenjioBundle\Security;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CorsListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if ($event->getRequest()->getMethod() === 'OPTIONS') {
            $response = $this->getPreflightResponse();
        }

        $responseHeaders = $response->headers;

        $responseHeaders->set('Access-Control-Allow-Headers', 'origin, content-type, accept, genjio-api-key, genjio-api-username');
        $responseHeaders->set('Access-Control-Allow-Origin', '*');
        $responseHeaders->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');
        $event->setResponse($response);
    }

    private function getPreflightResponse($event)
    {
        $response = new Response();
        return $response;
    }
}
